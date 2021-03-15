<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class SellerProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Seller $seller)
    {
        $products = $seller->products;

        return $this->showAll($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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

        $validatedData['seller_id'] = $seller->id;

        $product = $seller->products()->create($validatedData);

        return $this->showOne($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Http\Response
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
                'required'
            ]
        ]);

        if (!$product->isTheProductSeller($seller)) {
            return $this->errorResponse('The specified seller is not the actual seller of the product', 422);
        }

        if ($product->isAvailable() && count($product->categories) > 0) {
            return $this->errorResponse('An active product must have at least one category', 409);
        }

        $product->update($validatedAttributes);

        return $this->showOne($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Seller  $seller
     * @return \Illuminate\Http\Response
     */
    public function destroy(Seller $seller, Product $product)
    {
        if (!$product->isTheProductSeller($seller)) {
            return $this->errorResponse('The specified seller is not the actual seller of the product', 422);
        }

        $product->delete();

        return response('', 204);
    }
}
