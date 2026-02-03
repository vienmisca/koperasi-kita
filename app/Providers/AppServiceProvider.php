<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share pending password reset count with admin layout
        view()->composer('layouts.admin', function ($view) {
            $pendingResetCount = \App\Models\User::whereNotNull('pending_password')->count();
            $view->with('pendingResetCount', $pendingResetCount);
        });
    }
}
