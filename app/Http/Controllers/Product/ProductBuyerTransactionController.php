<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Models\Buyer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductBuyerTransactionController extends ApiController
{
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
            return response('The buyer must be different from the seller', 409);
        }

        if (!$buyer->isVerified()) {
            return response('The buyer must be different a verified user', 409);
        }

        if (!$product->isAvailable()) {
            return response('The product is not available', 409);
        }

        if ($product->quantity < request('quantity')) {
            return response('The product does not have enough units for this transaction', 409);
        }

        return DB::transaction(function () use ($validatedAttributes, $product, $buyer) {
            $product->quantity -= request('quantity');
            $product->save();

            $validatedAttributes['buyer_id'] = $buyer->id;

            $transaction = $product->transactions()->create($validatedAttributes);

            return $this->showOne($transaction);
        });
    }
}