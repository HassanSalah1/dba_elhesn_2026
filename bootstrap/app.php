<?php

use App\Http\Middleware\AuthenticateApi;
use App\Http\Middleware\AuthenticateSite;
use App\Http\Middleware\AuthWare;
use App\Http\Middleware\GuestSite;
use App\Http\Middleware\Lang;
use App\Http\Middleware\LocaleMiddleware;
use App\Http\Middleware\UnverifiedAuthenticateSite;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            require __DIR__ . '/../routes/panel.php';
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'admin' => AuthWare::class,
            'lang' => Lang::class,
            'authApi' => AuthenticateApi::class,

            'localize' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeViewPath' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,

            'guest-site' => GuestSite::class,
            'auth-site' => AuthenticateSite::class,
            'unverified-site' => UnverifiedAuthenticateSite::class,
        ]);

        // Preserve existing behavior from Laravel 10 Kernel web group
        $middleware->appendToGroup('web', LocaleMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
