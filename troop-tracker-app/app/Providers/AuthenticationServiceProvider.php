<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\AuthenticationInterface;
use App\Services\StandaloneService;
use App\Services\XenforoService;
use Illuminate\Support\ServiceProvider;

class AuthenticationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AuthenticationInterface::class, function ($app)
        {
            $type = config('tracker.plugins.type');

            return match ($type)
            {
                'xenforo' => new XenforoService(),
                default => new StandaloneService(),
            };
        });
    }


    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
