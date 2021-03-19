<?php

namespace App\Transformers;

use App\Models\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Category $category)
    {
        return [
            'identifier' => (int) $category->id,
            'title' => (string) $category->name,
            'details' => (string) $category->description,
            'creationDate' => $category->created_at,
            'lastChange' => $category->updated_at,
            'deletedDate' => isset($category->deleted_at) ? (string) $category->deleted_at : null
        ];
    }

    public static function originalAttribute($attribute)
    {
        $attributes = [
            'identifier' => 'id',
            'title' => 'name',
            'details' => 'description',
            'creationDate' => 'created_at',
            'lastChange' => 'updated_at',
            'deletedDate' => 'deleted_at'
        ];

        return isset($attributes[$attribute]) ? $attributes[$attribute] : null;
    }
}
