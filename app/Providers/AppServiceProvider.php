<?php

namespace App\Providers;

use App\Support\BlindIndex;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        RateLimiter::for('login', function (Request $request): Limit {
            $emailHash = BlindIndex::email($request->string('email')->toString());

            return Limit::perMinute(5)->by($emailHash.'|'.$request->ip());
        });

        RateLimiter::for('password-recovery', function (Request $request): Limit {
            $emailHash = BlindIndex::email($request->string('email')->toString());

            return Limit::perMinute(3)->by($emailHash.'|'.$request->ip());
        });
    }
}
