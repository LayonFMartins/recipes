<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GreetingService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GreetingService::class, function () {
        return new GreetingService('Laio');
    });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
