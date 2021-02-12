<?php

use App\Http\Controllers\Buyer\BuyerController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Seller\SellerController;
use App\Http\Controllers\Transaction\TransactionController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::resource('buyer', BuyerController::class, ['only' => ['index', 'show']]);
Route::resource('seller', SellerController::class, ['only' => ['index', 'show']]);
Route::resource('category', CategoryController::class, ['except' => ['create', 'edit']]);
Route::resource('product', ProductController::class, ['only' => ['index', 'show']]);
Route::resource('transaction', TransactionController::class, ['only' => ['index', 'show']]);
Route::resource('user', UserController::class, ['except' => ['create', 'edit']]);
