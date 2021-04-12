<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Models\Product;

class ProductController extends ApiController
{
    public function __construct()
    {
        $this->middleware('client.credentials');
    }

    /**
     * @OA\Get(
     *      path="/products",
     *      operationId="getProductsList",
     *      tags={"Products"},
     *      summary="Get list of products",
     *      description="Returns list of products",
     *      security={
     *          {"development": {}},
     *          {"production": {}},
     *      },
     *      @OA\Parameter(
     *          name="stock",
     *          description="List all products with the specified stock amount",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="situation",
     *          description="List all products with the specified situation",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="order_by",
     *          description="Product property to sort sort the data by",
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
     *          description="Returns when product is not authenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Returns when product is not authorized to perform this request",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="This action is unauthorized"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Returns when there's some problem with the application. Please report to the development team when getting this response.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Server Error"),
     *          )
     *      ),
     *  )
     */
    public function index()
    {
        $products = Product::all();

        return $this->showAll($products);
    }

    /**
     * @OA\Get(
     *      path="/products/{product}",
     *      operationId="getProductById",
     *      tags={"Products"},
     *      summary="Get product information",
     *      description="Returns product data",
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
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Returns when product is not authenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Returns when product is not authorized to perform this request",
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
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Returns when there's some problem with the application. Please report to the development team when getting this response.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Server Error"),
     *          )
     *      ),
     * )
     */
    public function show(Product $product)
    {
        return $this->showOne($product);
    }
}
