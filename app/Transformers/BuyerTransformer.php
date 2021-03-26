<?php

namespace App\Transformers;

use App\Models\Buyer;
use App\Models\Seller;
use League\Fractal\TransformerAbstract;

class BuyerTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Buyer $buyer)
    {
        return [
            'identifier' => (int) $buyer->id,
            'name' => (string) $buyer->name,
            'email' => (string) $buyer->email,
            'isVerified' => (int) $buyer->verified,
            'registeredAt' => $buyer->created_at,
            'lastChange' => $buyer->updated_at,
            'deletedDate' => isset($buyer->deleted_at) ? (string) $buyer->deleted_at : null,
            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('buyers.show', $buyer->id)
                ],
                [
                    'rel' => 'parent',
                    'href' => route('users.show', $buyer->id)
                ],
                [
                    'rel' => 'buyers.categories',
                    'href' => route('buyers.categories', $buyer->id)
                ],
                [
                    'rel' => 'buyers.products',
                    'href' => route('buyers.products', $buyer->id)
                ],
                [
                    'rel' => 'buyers.sellers',
                    'href' => route('buyers.sellers', $buyer->id)
                ],
                [
                    'rel' => 'buyers.transactions',
                    'href' => route('buyers.transactions', $buyer->id)
                ],
            ]
        ];
    }

    public static function originalAttribute($attribute)
    {
        $attributes = [
            'identifier' => 'id',
            'name' => 'name',
            'email' => 'email',
            'isVerified' => 'verified',
            'registeredAt' => 'created_at',
            'lastChange' => 'updated_at',
            'deletedDate' => 'deleted_at'
        ];

        return isset($attributes[$attribute]) ? $attributes[$attribute] : null;
    }
}
