<?php

namespace App\Providers;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(UrlGenerator $url): void
    {
        // Se estiver em produção, força HTTPS
        if ($this->app->environment('production')) {
            $url->forceScheme('https');
        }
    }
}
