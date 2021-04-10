<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Models\Seller;
use Illuminate\Http\Request;

class SellerCategoryController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('scope:read-general');
        $this->middleware('can:view,seller');
    }

    /**
     * @OA\Get(
     *      path="/sellers/{seller}/categories",
     *      operationId="getSellerTransactionsList",
     *      tags={"Sellers"},
     *      summary="Get list of seller's categories",
     *      description="Returns list of seller's categories",
     *      security={
     *          {"passport": {}},
     *      },
     *      @OA\Parameter(
     *          name="seller",
     *          description="Seller id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="order_by",
     *          description="Transaction property to sort sort the data by",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="per_page",
     *          description="How many records to return per page",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="page",
     *          description="Page number",
     *          required=false,
     *          in="query",
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
     *          description="Returns when category is not authenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Returns when category is not authorized to perform this request",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="This action is unauthorized"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      )
     *  )
     */
    public function __invoke(Seller $seller)
    {
        $categories = $seller->products()->whereHas('categories')
            ->with('categories')->get()->pluck('categories')
            ->collapse()->unique('id')->values();

        return $this->showAll($categories);
    }
}
