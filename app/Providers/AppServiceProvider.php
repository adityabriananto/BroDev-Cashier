<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
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
        //
        $start = defined('LARAVEL_START') ? LARAVEL_START : microtime(true);
        View::composer('*', function ($view) use ($start) {
            $executionTime = microtime(true) - $start;
            $memoryUsage = memory_get_peak_usage(true) / 1024 / 1024;

            $view->with('performance', [
                'time' => number_format($executionTime, 3),
                'memory' => number_format($memoryUsage, 1),
            ]);
        });
    }
}
