<?php

namespace Monsefeledrisse\FilamentSolarIcons;

use BladeUI\Icons\Factory;
use Illuminate\Support\ServiceProvider;
use Monsefeledrisse\FilamentSolarIcons\Commands\SolarIconBrowserCommand;

class SolarIconSetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerIcons();
        $this->registerCommands();
        $this->publishConfiguration();
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/solar-icons.php',
            'solar-icons'
        );
    }

    protected function registerIcons()
    {
        $iconSets = config('solar-icons.icon_sets', [
            'solar-bold' => [
                'enabled' => true,
                'path' => __DIR__ . '/../resources/icons/solar/solar-bold',
            ],
            'solar-bold-duotone' => [
                'enabled' => true,
                'path' => __DIR__ . '/../resources/icons/solar/solar-bold-duotone',
            ],
            'solar-broken' => [
                'enabled' => true,
                'path' => __DIR__ . '/../resources/icons/solar/solar-broken',
            ],
            'solar-line-duotone' => [
                'enabled' => true,
                'path' => __DIR__ . '/../resources/icons/solar/solar-line-duotone',
            ],
            'solar-linear' => [
                'enabled' => true,
                'path' => __DIR__ . '/../resources/icons/solar/solar-linear',
            ],
            'solar-outline' => [
                'enabled' => true,
                'path' => __DIR__ . '/../resources/icons/solar/solar-outline',
            ],
        ]);

        /** @var Factory $icons */
        $icons = $this->app->make(Factory::class);

        foreach ($iconSets as $prefix => $config) {
            if (!$config['enabled']) {
                continue;
            }

            $path = $config['path'] ?? __DIR__ . "/../resources/icons/solar/{$prefix}";

            if (is_dir($path)) {
                $icons->add($prefix, [
                    'path' => $path,
                    'prefix' => $prefix,
                ]);
            }
        }
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SolarIconBrowserCommand::class,
            ]);
        }
    }

    protected function publishConfiguration()
    {
        $this->publishes([
            __DIR__ . '/../config/solar-icons.php' => config_path('solar-icons.php'),
        ], 'solar-icons-config');
    }
}
