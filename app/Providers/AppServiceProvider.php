<?php

namespace App\Providers;

use App\Services\Payment\PaymentGatewayRegistry;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentGatewayRegistry::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $cartCount = 0;
            $wishlistCount = 0;

            if (Auth::check()) {
                $cartCount = Auth::user()->cartItems()->sum('quantity');
                $wishlistCount = Auth::user()->wishlists()->count();
            }

            $view->with([
                'globalCartCount' => $cartCount,
                'globalWishlistCount' => $wishlistCount,
            ]);
        });
    }
}
