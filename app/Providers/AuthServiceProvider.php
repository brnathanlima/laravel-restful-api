<?php

namespace App\Providers;

use App\Models\Buyer;
use App\Models\Seller;
use App\Policies\BuyerPolicy;
use App\Policies\SellerPolicy;
use Carbon\Carbon;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport as Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Buyer::class => BuyerPolicy::class,
        Seller::class => SellerPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (! $this->app->routesAreCached()) {
            Passport::routes();
        }

        Passport::tokensExpireIn(Carbon::now()->addMinutes(30));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));

        Passport::tokensCan([
            'purchase-product' => 'Create a new transaction for a specific product.',
            'manage-products' => 'Create, read, update and delete products (CRUD).',
            'manage-account' => 'Read your account data (except password) and modify it. Cannot delete.',
            'read-general' => 'Read General information like purchasing categories, purchased products,
                selling products, selling categories, your transactions (purchases ans sales).'
        ]);
    }
}
