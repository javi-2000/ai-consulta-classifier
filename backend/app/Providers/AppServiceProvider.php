<?php

namespace App\Providers;

use App\Contracts\AiClassificationServiceInterface;
use App\Services\AiClassificationService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            AiClassificationServiceInterface::class,
            AiClassificationService::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
