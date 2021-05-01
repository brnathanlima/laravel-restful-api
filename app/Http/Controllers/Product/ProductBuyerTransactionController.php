<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Models\Buyer;
use App\Models\Product;
use App\Transformers\TransactionTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductBuyerTransactionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('transform.input:' . TransactionTransformer::class);
        $this->middleware('scope:purchase-product');
        $this->middleware('can:purchase,buyer');
    }

    /**
     * @OA\POST(
     *      path="/products/{product}/buyers/{buyer}/transactions",
     *      operationId="postProductBuyerTransaction",
     *      tags={"Products"},
     *      summary="Creates a product transaction for the buyer",
     *      description="Returns the product transaction for the buyer's data",
     *      security={
     *          {"development": {}},
     *          {"production": {}},
     *      },
     *      @OA\Parameter(
     *          name="product",
     *          description="Product id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="buyer",
     *          description="Buyer id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="quantity",
     *                      type="integer",
     *                  ),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Created",
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
     *          description="Returns when there's not a product or buyer with the provided id",
     *          @OA\JsonContent(
     *              oneOf={
     *                  @OA\Property(
     *                      @OA\Property(property="error", type="string", example="Does not exist any product with the specified identificator."),
     *                      @OA\Property(property="code", type="integer", example="404"),
     *                  ),
     *                  @OA\Property(
     *                      @OA\Property(property="error", type="string", example="Does not exist any buyer with the specified identificator."),
     *                      @OA\Property(property="code", type="integer", example="404"),
     *                  ),
     *              }
     *          )
     *      ),
     *      @OA\Response(
     *          response=409,
     *          description="Returns when there's some validation trouble",
     *          @OA\JsonContent(
     *              oneOf={
     *                  @OA\Property(
     *                      @OA\Property(property="error", type="string", example="The buyer must be different from the seller"),
     *                      @OA\Property(property="code", type="integer", example="409"),
     *                  ),
     *                  @OA\Property(
     *                      @OA\Property(property="error", type="string", example="The buyer must be a verified user"),
     *                      @OA\Property(property="code", type="integer", example="409"),
     *                  ),
     *                  @OA\Property(
     *                      @OA\Property(property="error", type="string", example="The product is not available"),
     *                      @OA\Property(property="code", type="integer", example="409"),
     *                  ),
     *                  @OA\Property(
     *                      @OA\Property(property="error", type="string", example="The product does not have enough units for this transaction"),
     *                      @OA\Property(property="code", type="integer", example="409"),
     *                  ),
     *              },
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
     * )
     */
    public function __invoke(Product $product, Buyer $buyer)
    {
        $validatedAttributes = request()->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1'
            ]
        ]);

        if ($buyer->id === $product->seller->id) {
            return $this->errorResponse('The buyer must be different from the seller', 409);
        }

        if (!$buyer->isVerified()) {
            return $this->errorResponse('The buyer must be a verified user', 409);
        }

        if (!$product->isAvailable()) {
            return $this->errorResponse('The product is not available', 409);
        }

        if ($product->quantity < request('quantity')) {
            return $this->errorResponse('The product does not have enough units for this transaction', 409);
        }

        return DB::transaction(function () use ($validatedAttributes, $product, $buyer) {
            $product->quantity -= request('quantity');
            $product->save();

            $validatedAttributes['buyer_id'] = $buyer->id;

            $transaction = $product->transactions()->create($validatedAttributes);

            return $this->showOne($transaction, 201);
        });
    }
}
