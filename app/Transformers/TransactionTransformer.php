<?php

namespace App\Transformers;

use App\Models\Transaction;
use League\Fractal\TransformerAbstract;

class TransactionTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Transaction $transaction)
    {
        return [
            'identifier' => (int) $transaction->id,
            'quantity' => (int) $transaction->quantity,
            'buyer' => (int) $transaction->buyer_id,
            'product' => (int) $transaction->product_id,
            'creationDate' => $transaction->created_at,
            'lastChange' => $transaction->updated_at,
            'deletedDate' => isset($transaction->deleted_at) ? (string) $transaction->deleted_at : null
        ];
    }

    public static function originalAttribute($attribute)
    {
        $attributes = [
            'identifier' => 'id',
            'quantity' => 'quantity',
            'buyer' => 'buyer_id',
            'product' => 'product_id',
            'creationDate' => 'created_at',
            'lastChange' => 'updated_at',
            'deletedDate' => 'deleted_at'
        ];

        return isset($attributes[$attribute]) ? $attributes[$attribute] : null;
    }
}
