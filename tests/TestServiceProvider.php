<?php

declare(strict_types=1);

namespace Monsefeledrisse\FilamentSolarIcons\Tests;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Monsefeledrisse\FilamentSolarIcons\Tests\Commands\TestSolarIconsIntegrationCommand;

/**
 * Test Service Provider for Solar Icons Integration Testing
 * 
 * This service provider registers test routes, views, and commands
 * for comprehensive testing of Solar Icons integration.
 */
class TestServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register test commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                TestSolarIconsIntegrationCommand::class,
            ]);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only load test routes and views in development/testing environments
        if ($this->app->environment(['local', 'testing']) || config('app.debug')) {
            $this->loadTestRoutes();
            $this->loadTestViews();
        }
    }

    /**
     * Load test routes
     */
    private function loadTestRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/test-routes.php');
    }

    /**
     * Load test views
     */
    private function loadTestViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/views', 'solar-icons-test');
        
        // Add view namespace for easier access
        View::addNamespace('solar-test', __DIR__ . '/views');
    }
}
