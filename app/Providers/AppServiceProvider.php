<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GreetingService;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

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
        Gate::define("access-admin-area", function (User $user){
            return $user->isAdmin();
        });
    }
}
