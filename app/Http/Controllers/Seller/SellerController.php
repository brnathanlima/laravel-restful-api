<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Models\Seller;

class SellerController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('scope:read-general')->only('show');
        $this->middleware('can:view,seller')->only('show');
    }

    /**
     * @OA\Get(
     *      path="/sellers",
     *      operationId="getSellersList",
     *      tags={"Sellers"},
     *      summary="Get list of sellers",
     *      description="Returns list of sellers",
     *      security={
     *          {"passport": {}},
     *      },
     *      @OA\Parameter(
     *          name="isVerified",
     *          description="List all verified users",
     *          required=false,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="order_by",
     *          description="Seller property to sort sort the data by",
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
     *  )
     */
    public function index()
    {
        $this->allowedAdminAction();

        $sellers = Seller::all();

        return $this->showAll($sellers);
    }

    /**
     * @OA\Get(
     *      path="/sellers/{id}",
     *      operationId="getSellerById",
     *      tags={"Sellers"},
     *      summary="Get seller information",
     *      description="Returns seller data",
     *      security={
     *          {"passport": {}},
     *      },
     *      @OA\Parameter(
     *          name="id",
     *          description="Seller id",
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
     *      )
     * )
     */
    public function show(Seller $seller)
    {
        return $this->showOne($seller);
    }
}
