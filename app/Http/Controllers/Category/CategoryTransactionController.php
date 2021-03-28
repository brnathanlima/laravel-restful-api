<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryTransactionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __invoke(Category $category)
    {
        $transactions = $category->products()->whereHas('transactions')
            ->with('transactions')->get()->pluck('transactions')->collapse();

        return $this->showAll($transactions);
    }
}
