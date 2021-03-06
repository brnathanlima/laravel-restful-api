<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\ApiController;
use App\Models\Seller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionSellerController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('scope:read-general');
        $this->middleware('can:view,transaction');
    }

    /**
     * @OA\Get(
     *      path="/transactions/{transaction}/sellers",
     *      operationId="getTransactionSeller",
     *      tags={"Transactions"},
     *      summary="Get the transaction's seller",
     *      description="Returns the transaction's seller",
     *      security={
     *          {"development": {}},
     *          {"production": {}},
     *      },
     *      @OA\Parameter(
     *          name="transaction",
     *          description="Transaction id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Returns when user is not authenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Unauthenticated."),
     *              @OA\Property(property="code", type="integer", example="401"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Returns when user is not authorized to perform this request",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Invalid scopes provided."),
     *              @OA\Property(property="code", type="integer", example="403"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Returns when there's not a transaction with the provided id",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Does not exist any transaction with the specified identificator."),
     *              @OA\Property(property="code", type="integer", example="404"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Returns when there's some problem with the application. Please report to the development team when getting this response.",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="We are facing an unespected problem. Please try again later"),
     *              @OA\Property(property="code", type="integer", example="500"),
     *          )
     *      ),
     *  )
     */
    public function __invoke(Transaction $transaction)
    {
        $seller = $transaction->product()->with('seller')->get()
            ->pluck('seller')->first();

        if (is_null($seller)) {
            return new Seller;
        };

        return $this->showOne($seller);
    }
}
