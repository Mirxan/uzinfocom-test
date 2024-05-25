<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Mixins\ResponseFactoryMixin;
use Illuminate\Routing\ResponseFactory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        ResponseFactory::mixin(new ResponseFactoryMixin());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
