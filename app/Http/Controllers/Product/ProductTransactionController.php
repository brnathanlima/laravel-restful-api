<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\ApiController;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductTransactionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __invoke(Product $product)
    {
        $this->allowedAdminAction();

        $transactions = $product->transactions;

        return $this->showAll($transactions);
    }
}
