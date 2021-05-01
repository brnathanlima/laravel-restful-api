<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\ApiController;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryTransactionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @OA\Get(
     *      path="/categories/{category}/transactions",
     *      operationId="getCategoryTransactionsList",
     *      tags={"Categories"},
     *      summary="Get list of category's transactions",
     *      description="Returns list of category's transactions",
     *      security={
     *          {"development": {}},
     *          {"production": {}},
     *      },
     *      @OA\Parameter(
     *          name="category",
     *          description="Category id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="quantity",
     *          description="List all category's transaction with the specified quantity of products",
     *          required=false,
     *          in="query",
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
     *          description="Returns when there's not a category with the provided id",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="Does not exist any category with the specified identificator."),
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
    public function __invoke(Category $category)
    {
        $this->allowedAdminAction();

        $transactions = $category->products()->whereHas('transactions')
            ->with('transactions')->get()->pluck('transactions')->collapse();

        return $this->showAll($transactions);
    }
}
