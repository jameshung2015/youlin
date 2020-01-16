<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\HwtCustomAuth;
class CustomLoginProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('our_auth', function ($app) {
            return new HwtCustomAuth();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
