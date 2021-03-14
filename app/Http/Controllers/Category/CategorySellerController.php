<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategorySellerController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Category $category)
    {
        $sellers = $category->products()->with('seller')->get()
            ->pluck('seller')->unique()->values();

        return $this->showAll($sellers);
    }
}
