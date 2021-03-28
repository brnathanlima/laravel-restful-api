<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\ApiController;
use App\Models\Seller;
use Illuminate\Http\Request;

class SellerCategoryController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('scope:read-general');
        $this->middleware('can:view,seller');
    }

    public function __invoke(Seller $seller)
    {
        $categories = $seller->products()->whereHas('categories')
            ->with('categories')->get()->pluck('categories')
            ->collapse()->unique('id')->values();

        return $this->showAll($categories);
    }
}
