<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiting\Limit;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Share auth user to all views
        View::composer('*', function ($view) {
            $view->with('authUser', auth()->user());
        });

        // Per-survey-token rate limit: 200 submissions per hour per assessment.
        // Prevents one tenant's mass survey blast from starving other tenants.
        RateLimiter::for('survey-by-token', function (Request $request) {
            return Limit::perHour(200)->by('survey:' . $request->route('token'));
        });

        // Per-company authenticated rate limit: 60 req/min per company_id.
        // Ensures no single tenant monopolises PHP workers.
        RateLimiter::for('tenant', function (Request $request) {
            $companyId = $request->user()?->company_id ?? $request->ip();
            return Limit::perMinute(60)->by('tenant:' . $companyId);
        });
    }
}
