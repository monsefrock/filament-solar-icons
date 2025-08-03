<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Monsefeledrisse\FilamentSolarIcons\Tests\TestController;

/*
|--------------------------------------------------------------------------
| Solar Icons Test Routes
|--------------------------------------------------------------------------
|
| These routes are used for testing Solar Icons integration with 
| blade-ui-kit/blade-icons package. They provide both web interface
| and API endpoints for comprehensive testing.
|
*/

// Main test page route
Route::get('/solar-icons/test', [TestController::class, 'index'])
    ->name('solar-icons.test.index');

// AJAX endpoint for testing individual icons
Route::post('/solar-icons/test/icon', [TestController::class, 'testIcon'])
    ->name('solar-icons.test.icon');

// API endpoint for icon sets information
Route::get('/solar-icons/test/icon-sets', [TestController::class, 'iconSets'])
    ->name('solar-icons.test.icon-sets');

// API endpoint for enum information
Route::get('/solar-icons/test/enum', [TestController::class, 'testEnum'])
    ->name('solar-icons.test.enum');

// Health check endpoint
Route::get('/solar-icons/test/health', function () {
    try {
        $factory = app(\BladeUI\Icons\Factory::class);
        $registeredSets = $factory->all();
        $solarSets = array_filter($registeredSets, function ($key) {
            return str_starts_with($key, 'solar-');
        }, ARRAY_FILTER_USE_KEY);

        return response()->json([
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'solar_sets_registered' => count($solarSets),
            'solar_sets' => array_keys($solarSets),
            'blade_icons_available' => class_exists(\BladeUI\Icons\Factory::class),
            'service_provider_loaded' => app()->getLoadedProviders()[\Monsefeledrisse\FilamentSolarIcons\SolarIconSetServiceProvider::class] ?? false,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'error',
            'timestamp' => now()->toISOString(),
            'error' => $e->getMessage(),
            'blade_icons_available' => class_exists(\BladeUI\Icons\Factory::class),
        ], 500);
    }
})->name('solar-icons.test.health');
