<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Models\Buyer;
use Illuminate\Http\Request;

class BuyerTransactionController extends ApiController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('scope:read-general');
        $this->middleware('can:view,buyer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Buyer $buyer)
    {
        $transactions = $buyer->transactions;

        return $this->showAll($transactions);
    }
}
