<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Models\Product;
use App\Models\Seller;
use App\Transformers\ProductTransformer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SellerProductController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('transform.input:' . ProductTransformer::class)->only(['store', 'update']);
        $this->middleware('scope:manage-products')->except('index');
        $this->middleware('can:view,seller')->only('index');
        $this->middleware('can:sale,seller')->only('store');
        $this->middleware('can:edit-product,seller')->only('update');
        $this->middleware('can:delete-product,seller')->only('destroy');
    }

    /**
     * @OA\Get(
     *      path="/sellers/{seller}/products",
     *      operationId="getSellerTransactionsList",
     *      tags={"Sellers"},
     *      summary="Get list of seller's products",
     *      description="Returns list of seller's products",
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
     *          name="stock",
     *          description="List all seller's products with the specified stock amount",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="situation",
     *          description="List all seller's products with the specified situation",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="string"
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
     *          description="Returns when seller is not authenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Returns when seller is not authorized to perform this request",
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
    public function index(Seller $seller)
    {
        if (!(request()->user()->tokenCan('read-general') || request()->user()->tokenCan('manage-products'))) {
            throw new AuthorizationException('Invalide scope(s)');
        }

        $products = $seller->products;

        return $this->showAll($products);
    }

    /**
     * @OA\Post(
     *      path="/sellers/{seller}/products",
     *      operationId="storeSellerProduct",
     *      tags={"Sellers"},
     *      summary="Store new seller's product",
     *      description="Returns seller's product data",
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
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="title",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="details",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="stock",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="image",
     *                      type="file",
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
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Returns when user is not authorized to perform this request",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="This action is unauthorized"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Returns when there's some validation trouble",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(
     *                  property="errors",
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="email", type="string", example="The email has already been taken."),
     *                  ),
     *              )
     *          )
     *      ),
     * )
     */
    public function store(Seller $seller)
    {
        $validatedData = request()->validate([
            'name' => [
                'string',
                'required',
                'min:5',
                'max:255'
            ],
            'description' => [
                'string',
                'required'
            ],
            'quantity' => [
                'integer',
                'required',
                'min:1'
            ],
            'image' => [
                'nullable',
                'image'
            ]
        ]);

        $validatedData['image'] = request()->file('image')->store('');
        $validatedData['status'] = Product::UNAVAILABLE_PRODUCT;
        $validatedData['seller_id'] = $seller->id;

        $product = $seller->products()->create($validatedData);

        return $this->showOne($product);
    }

    /**
     * @OA\POST(
     *      path="/sellers/{seller}/products/{product}",
     *      operationId="upadateSellersProduct",
     *      tags={"Sellers"},
     *      summary="Update a seller's product",
     *      description="Returns seller's product updated data",
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
     *          name="product",
     *          description="Product id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="_method",
     *          description="Method",
     *          required=true,
     *          in="query",
     *          example="PUT",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="title",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="details",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="stock",
     *                      type="integer",
     *                  ),
     *                  @OA\Property(
     *                      property="situation",
     *                      type="string",
     *                  ),
     *                  @OA\Property(
     *                      property="image",
     *                      type="file",
     *                  ),
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful Operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Returns when user is not authenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Returns when user is not authorized to perform this request",
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
     *          response=409,
     *          description="Returns when there's some validation trouble",
     *          @OA\JsonContent(
     *                      @OA\Property(property="error", type="string", example="An active product must have at least one category"),
     *                      @OA\Property(property="code", type="integer", example="409"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Returns when there's some validation trouble",
     *          @OA\JsonContent(
     *              oneOf={
     *                  @OA\Property(
     *                      @OA\Property(property="message", type="string", example="The given data was invalid."),
     *                      @OA\Property(
     *                          property="errors",
     *                          type="array",
     *                          @OA\Items(
     *                              @OA\Property(property="email", type="string", example="The email has already been taken."),
     *                          ),
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      @OA\Property(property="message", type="string", example="The specified seller is not the actual seller of the product"),
     *                      @OA\Property(property="code", type="integer", example="422"),
     *                  ),
     *              }
     *          )
     *      ),
     * )
     */
    public function update(Seller $seller, Product $product)
    {
        $validatedAttributes = request()->validate([
            'name' => [
                'string',
                'required',
                'min:5',
                'max:255'
            ],
            'description' => [
                'string',
                'required'
            ],
            'quantity' => [
                'required',
                'integer',
                'min:1'
            ],
            'status' => [
                'required',
                'in:' . Product::AVAILABLE_PRODUCT . ',' . Product::UNAVAILABLE_PRODUCT
            ],
            'image' => [
                'nullable',
                'image'
            ]
        ]);

        if (!$product->isTheProductSeller($seller)) {
            return $this->errorResponse('The specified seller is not the actual seller of the product', 422);
        }

        if ($product->isAvailable() && count($product->categories) > 0) {
            return $this->errorResponse('An active product must have at least one category', 409);
        }

        if (request()->hasFile('image')) {
            Storage::delete($product->image);

            $validatedAttributes['image'] = request()->file('image')->store('');
        }

        $product->update($validatedAttributes);

        return $this->showOne($product);
    }

    /**
     * @OA\DELETE(
     *      path="/sellers/{seller}/products/{product}",
     *      operationId="deleteSellersProduct",
     *      tags={"Sellers"},
     *      summary="Deletes a seller's product and return no content",
     *      description="Return",
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
     *          name="product",
     *          description="Product id",
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
     *          response=401,
     *          description="Returns when user is not authenticated",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Unauthenticated"),
     *          )
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Returns when user is not authorized to perform this request",
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
     *          response=422,
     *          description="Returns when there's some validation trouble",
     *          @OA\JsonContent(
     *                      @OA\Property(property="error", type="string", example="The specified seller is not the actual seller of the product"),
     *                      @OA\Property(property="code", type="integer", example="422"),
     *          )
     *      ),
     * )
     */
    public function destroy(Seller $seller, Product $product)
    {
        if (!$product->isTheProductSeller($seller)) {
            return $this->errorResponse('The specified seller is not the actual seller of the product', 422);
        }

        $product->delete();

        Storage::delete($product->image);

        return response('', 204);
    }
}
