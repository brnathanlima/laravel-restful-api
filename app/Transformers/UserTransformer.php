<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'identifier' => (int) $user->id,
            'name' => (string) $user->name,
            'email' => (string) $user->email,
            'isVerified' => (int) $user->verified,
            'isAdmin' => ($user->admin === 'true'),
            'registeredAt' => $user->created_at,
            'lastChange' => $user->updated_at,
            'deletedDate' => isset($user->deleted_at) ? (string) $user->deleted_at : null,
            'links' => [
                [
                    'rel' => 'self',
                    'href' => route('users.show', $user->id)
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
            'isAdmin' => 'admin',
            'password' => 'password',
            'passwordConfirmation' => 'password_confirmation',
            'registeredAt' => 'created_at',
            'lastChange' => 'updated_at',
            'deletedDate' => 'deleted_at'
        ];

        return isset($attributes[$attribute]) ? $attributes[$attribute] : null;
    }

    public static function transformedAttribute($attribute)
    {
        $attributes = [
            'id' => 'identifier',
            'name' => 'name',
            'email' => 'email',
            'verified' => 'isVerified',
            'admin' => 'isAdmin',
            'password' => 'password',
            'password_confirmation' => 'passwordConfirmation',
            'created_at' => 'registeredAt',
            'updated_at' => 'lastChange',
            'deleted_at' => 'deletedDate'
        ];

        return isset($attributes[$attribute]) ? $attributes[$attribute] : null;
    }
}
