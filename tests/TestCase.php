<?php

namespace Monsefeledrisse\FilamentSolarIcons\Tests;

use BladeUI\Icons\BladeIconsServiceProvider;
use Monsefeledrisse\FilamentSolarIcons\SolarIconSetServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            BladeIconsServiceProvider::class,
            SolarIconSetServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Set up any environment configuration needed for tests
    }
}
