<?php

use App\Http\Controllers\Buyer\BuyerCategoryController;
use App\Http\Controllers\Buyer\BuyerController;
use App\Http\Controllers\Buyer\BuyerProductController;
use App\Http\Controllers\Buyer\BuyerSellerController;
use App\Http\Controllers\Buyer\BuyerTransactionController;
use App\Http\Controllers\Category\CategoryBuyerController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Category\CategoryProductController;
use App\Http\Controllers\Category\CategorySellerController as CategoryCategorySellerController;
use App\Http\Controllers\Category\CategoryTransactionController;
use App\Http\Controllers\CategorySellerController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Seller\SellerBuyerController;
use App\Http\Controllers\Seller\SellerCategoryController;
use App\Http\Controllers\Seller\SellerController;
use App\Http\Controllers\Seller\SellerTransactionController;
use App\Http\Controllers\Transaction\TransactionCategoryController;
use App\Http\Controllers\Transaction\TransactionController;
use App\Http\Controllers\Transaction\TransactionSellerController;
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

Route::resource('buyers', BuyerController::class, ['only' => ['index', 'show']]);
Route::resource('sellers', SellerController::class, ['only' => ['index', 'show']]);
Route::resource('categories', CategoryController::class, ['except' => ['create', 'edit']]);
Route::resource('products', ProductController::class, ['only' => ['index', 'show']]);
Route::resource('transactions', TransactionController::class, ['only' => ['index', 'show']]);
Route::resource('users', UserController::class, ['except' => ['create', 'edit']]);
Route::get('transactions/{transaction}/categories', TransactionCategoryController::class);
Route::get('transactions/{transaction}/sellers', TransactionSellerController::class);
Route::get('buyers/{buyer}/transactions', BuyerTransactionController::class);
Route::get('buyers/{buyer}/products', BuyerProductController::class);
Route::get('buyers/{buyer}/sellers', BuyerSellerController::class);
Route::get('buyers/{buyer}/categories', BuyerCategoryController::class);
Route::get('categories/{category}/products', CategoryProductController::class);
Route::get('categories/{category}/sellers', CategoryCategorySellerController::class);
Route::get('categories/{category}/transactions', CategoryTransactionController::class);
Route::get('categories/{category}/buyers', CategoryBuyerController::class);
Route::get('sellers/{seller}/transactions', SellerTransactionController::class);
Route::get('sellers/{seller}/categories', SellerCategoryController::class);
Route::get('sellers/{seller}/buyers', SellerBuyerController::class);
