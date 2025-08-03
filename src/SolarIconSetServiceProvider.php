<?php

namespace Monsefeledrisse\FilamentSolarIcons;

use BladeUI\Icons\Factory;
use Illuminate\Support\ServiceProvider;

class SolarIconSetServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerIcons();
    }

    protected function registerIcons()
    {
        $iconSets = [
            'solar-bold' => __DIR__ . '/../resources/icons/solar/solar-bold',
            'solar-bold-duotone' => __DIR__ . '/../resources/icons/solar/solar-bold-duotone',
            'solar-broken' => __DIR__ . '/../resources/icons/solar/solar-broken',
            'solar-line-duotone' => __DIR__ . '/../resources/icons/solar/solar-line-duotone',
            'solar-linear' => __DIR__ . '/../resources/icons/solar/solar-linear',
            'solar-outline' => __DIR__ . '/../resources/icons/solar/solar-outline',
        ];

        /** @var Factory $icons */
        $icons = $this->app->make(Factory::class);

        foreach ($iconSets as $prefix => $path) {
            if (is_dir($path)) {
                $icons->add($prefix, [
                    'path' => $path,
                    'prefix' => $prefix,
                ]);
            }
        }
    }
}
