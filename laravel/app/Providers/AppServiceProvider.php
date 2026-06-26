<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Share auth user to all views
        View::composer('*', function ($view) {
            $view->with('authUser', auth()->user());
        });
    }
}
