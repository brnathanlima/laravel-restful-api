<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return response()->json(['data' => $users], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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

        return response()->json(['data' => $user], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json(['data' => $user], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
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
                return response()->json([
                    'error' => 'Only verified users can modify the admin field.',
                    'code' => 409
                ], 409);
            }

            $user->admin = $validatedData['admin'];
        }

        if (!$user->isDirty()) {
            return response()->json([
                'error' => 'You need to specify a different value to update.',
                'code' => 422
            ], 422);
        }

        $user->save();

        return response()->json(['data' => $user], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json("User with id of {$user->id} has been deleted.", 200);
    }
}
