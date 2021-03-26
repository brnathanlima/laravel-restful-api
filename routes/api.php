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
use App\Http\Controllers\Product\ProductBuyerController;
use App\Http\Controllers\Product\ProductBuyerTransactionController;
use App\Http\Controllers\Product\ProductCategoryController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\Product\ProductTransactionController;
use App\Http\Controllers\Seller\SellerBuyerController;
use App\Http\Controllers\Seller\SellerCategoryController;
use App\Http\Controllers\Seller\SellerController;
use App\Http\Controllers\Seller\SellerProductController;
use App\Http\Controllers\Seller\SellerTransactionController;
use App\Http\Controllers\Transaction\TransactionCategoryController;
use App\Http\Controllers\Transaction\TransactionController;
use App\Http\Controllers\Transaction\TransactionSellerController;
use App\Http\Controllers\User\UserController;
use App\Models\Product;
use App\Models\Seller;
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

Route::resource('transactions', TransactionController::class, ['only' => ['index', 'show']]);
Route::prefix('transactions')->group(function () {
    Route::get('{transaction}/categories', TransactionCategoryController::class);
    Route::get('{transaction}/sellers', TransactionSellerController::class);
});

Route::resource('buyers', BuyerController::class, ['only' => ['index', 'show']]);
Route::prefix('buyers')->group(function () {
    Route::get('{buyer}/transactions', BuyerTransactionController::class);
    Route::get('{buyer}/products', BuyerProductController::class);
    Route::get('{buyer}/sellers', BuyerSellerController::class);
    Route::get('{buyer}/categories', BuyerCategoryController::class);
});

Route::resource('categories', CategoryController::class, ['except' => ['create', 'edit']]);
Route::prefix('categories')->group(function () {
    Route::get('{category}/sellers', CategoryCategorySellerController::class)->name('categories.sellers');
    Route::get('{category}/transactions', CategoryTransactionController::class)->name('categories.transactions');
    Route::get('{category}/buyers', CategoryBuyerController::class)->name('categories.buyers');
    Route::get('{category}/products', CategoryProductController::class)->name('categories.products');
});

Route::resource('sellers', SellerController::class, ['only' => ['index', 'show']]);
Route::prefix('sellers/{seller}')->group(function () {
    Route::get('transactions', SellerTransactionController::class);
    Route::get('categories', SellerCategoryController::class);
    Route::get('buyers', SellerBuyerController::class);
    Route::apiResource('products', SellerProductController::class)->except(['show']);
});

Route::resource('products', ProductController::class, ['only' => ['index', 'show']]);
Route::apiResource('products.categories', ProductCategoryController::class)->except(['store', 'show']);
Route::prefix('products/{product}')->group(function () {
    Route::get('transactions', ProductTransactionController::class)->name('products.transactions');
    Route::get('buyers', ProductBuyerController::class)->name('products.buyers');
    Route::post('buyers/{buyer}/transactions', ProductBuyerTransactionController::class);
});

Route::resource('users', UserController::class, ['except' => ['create', 'edit']]);
Route::prefix('users')->group(function () {
    Route::get('verify/{token}', [UserController::class, 'verity'])->name('verify');
    Route::get('{user}/resend', [UserController::class, 'resend'])->name('resend');
});
