<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\ApiController;
use App\Mail\UserCreated;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class UserController extends ApiController
{
    public function __construct()
    {
        $this->middleware('client.credentials')->only(['store', 'resend']);
        $this->middleware('auth:api')->except(['store', 'resend', 'verify']);
        $this->middleware('transform.input:' . UserTransformer::class)->only(['store', 'update']);
        $this->middleware('scope:manage-account')->only(['show', 'update']);
        $this->middleware('can:view,user')->only('show');
        $this->middleware('can:update,user')->only('update');
        $this->middleware('can:delete,user')->only('destroy');
    }

    /**
     * @OA\Get(
     *      path="/users",
     *      operationId="getUsersList",
     *      tags={"Users"},
     *      summary="Get list of users",
     *      description="Returns list of users",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *     )
     */
    public function index()
    {
        $this->allowedAdminAction();

        $users = User::all();

        return $this->showAll($users);
    }

    /**
     * @OA\Post(
     *      path="/users",
     *      operationId="storeUser",
     *      tags={"Users"},
     *      summary="Store new user",
     *      description="Returns user data",
     *      @OA\RequestBody(
     *          required=true,
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *      )
     * )
     */
    public function store()
    {
        $validatedData = request()->validate([
            'name' => [
                'string',
                'required',
                'max:255',
            ],
            'email' => [
                'required',
                'max:255',
                'email',
                'unique:users'
            ],
            'password' => [
                'required',
                'min:6',
                'confirmed'
            ]
        ]);

        $validatedData['password'] = bcrypt($validatedData['password']);
        $validatedData['verified'] = User::UNVERIFIED_USER;
        $validatedData['verification_token'] = User::generateVerificationCode();
        $validatedData['admin'] = User::REGULAR_USER;

        $user = User::create($validatedData);

        return $this->showOne($user, HttpResponse::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *      path="/users/{id}",
     *      operationId="getUserById",
     *      tags={"Users"},
     *      summary="Get user information",
     *      description="Returns user data",
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function show(User $user)
    {
        return $this->showOne($user);
    }

    /**
     * @OA\Put(
     *      path="/users/{id}",
     *      operationId="updateUser",
     *      tags={"Users"},
     *      summary="Update existing user",
     *      description="Returns updated user data",
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *      @OA\Response(
     *          response=409,
     *          description="Conflict"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function update(User $user)
    {
        $validatedData = request()->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'email' => [
                'required',
                'max:255',
                'email',
                Rule::unique('users')->ignore($user)
            ],
            'password' => [
                'required',
                'min:6'
            ],
            'admin' => [
                'nullable',

            ]
        ]);

        if (request()->has('name')) {
            $user->name = $validatedData['name'];
        }

        if (request()->has('email') && $user->email != $validatedData['email']) {
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerificationCode();
            $user->email = $validatedData['email'];
        }

        if (request()->has('password')) {
            $user->password = bcrypt($validatedData['password']);
        }

        if (request()->has('admin')) {
            if (!$user->isVerified()) {
                return $this->errorResponse(
                    'Only verified users can modify the admin field.',
                    HttpResponse::HTTP_CONFLICT
                );
            }

            $this->allowedAdminAction();

            $user->admin = $validatedData['admin'];
        }

        if (!$user->isDirty()) {
            return $this->errorResponse(
                'You need to specify a different value to update.',
                HttpResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $user->save();

        return $this->showOne($user);
    }

    /**
     * @OA\Delete(
     *      path="/users/{id}",
     *      operationId="deleteUser",
     *      tags={"Users"},
     *      summary="Delete existing user",
     *      description="Deletes a user data and returns no content",
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response(null, HttpResponse::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Get(
     *      path="/users/verify/{token}",
     *      operationId="verifyUser",
     *      tags={"Users"},
     *      summary="Verify existing user",
     *      description="Verify a existing user and return a success message",
     *      @OA\Parameter(
     *          name="token",
     *          description="User token",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function verify($token)
    {
        $user = User::where('verification_token', $token)->firstOrFail();

        $user->verified = User::VERIFIED_USER;
        $user->verification_token = null;

        $user->save();

        return $this->showMessage('The account has been successfully verified');
    }

    /**
     * @OA\Get(
     *      path="/users/{id}/resend",
     *      operationId="resendUserToken",
     *      tags={"Users"},
     *      summary="Resend user token",
     *      description="Resend through email the user token and return a success message",
     *      @OA\Parameter(
     *          name="id",
     *          description="User id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      @OA\Response(
     *          response=409,
     *          description="User already veiried",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     * )
     */
    public function resend(User $user)
    {
        if ($user->isVerified()) {
            return $this->errorResponse('This user is already verified', 409);
        }

        retry(5, function () use ($user) {
            Mail::to($user)->send(new UserCreated($user));
        }, 100);

        return $this->showMessage('The verification token has been resend');
    }
}
