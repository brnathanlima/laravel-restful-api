<?php

namespace App\Transformers;

use App\Models\Seller;
use League\Fractal\TransformerAbstract;

class SellerTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Seller $seller)
    {
        return [
            'identifier' => (int) $seller->id,
            'name' => (string) $seller->name,
            'email' => (string) $seller->email,
            'isVerified' => (int) $seller->verified,
            'registeredAt' => $seller->created_at,
            'lastChange' => $seller->updated_at,
            'deletedDate' => isset($seller->deleted_at) ? (string) $seller->deleted_at : null
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
