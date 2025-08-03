<?php

namespace Monsefeledrisse\LaravelSolarIcons\Tests;

use BladeUI\Icons\BladeIconsServiceProvider;
use BladeUI\Icons\Factory;
use Monsefeledrisse\LaravelSolarIcons\SolarIconSetServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class ConfigurationTest extends Orchestra
{
    protected function getPackageProviders($app)
    {
        // Only register BladeUI Icons, not our service provider
        return [
            BladeIconsServiceProvider::class,
        ];
    }

    /** @test */
    public function it_registers_all_configured_icon_sets()
    {
        config([
            'solar-icons.sets' => [
                'solar-linear',
                'solar-bold',
                'solar-outline',
            ],
        ]);

        $provider = new SolarIconSetServiceProvider($this->app);
        $provider->boot();

        $iconFactory = $this->app->make(Factory::class);
        $registeredSets = $iconFactory->all();
        $setNames = array_keys($registeredSets);

        // Should have the configured individual sets
        $this->assertTrue(in_array('solar-linear', $setNames), 'solar-linear set should be registered. Found sets: ' . implode(', ', $setNames));
        $this->assertTrue(in_array('solar-bold', $setNames), 'solar-bold set should be registered. Found sets: ' . implode(', ', $setNames));
        $this->assertTrue(in_array('solar-outline', $setNames), 'solar-outline set should be registered. Found sets: ' . implode(', ', $setNames));

        // Should also have the default combined set
        $this->assertTrue(in_array('default', $setNames), 'Default combined set should be registered. Found sets: ' . implode(', ', $setNames));
    }

    /** @test */
    public function it_registers_default_sets_when_no_config_provided()
    {
        // Don't set any configuration, should use defaults
        $provider = new SolarIconSetServiceProvider($this->app);
        $provider->boot();

        $iconFactory = $this->app->make(Factory::class);
        $registeredSets = $iconFactory->all();
        $setNames = array_keys($registeredSets);

        // Should have all default sets
        $expectedSets = [
            'solar-bold',
            'solar-bold-duotone',
            'solar-broken',
            'solar-line-duotone',
            'solar-linear',
            'solar-outline',
            'default'
        ];

        foreach ($expectedSets as $expectedSet) {
            $this->assertTrue(in_array($expectedSet, $setNames), "Set '{$expectedSet}' should be registered. Found sets: " . implode(', ', $setNames));
        }
    }

    /** @test */
    public function it_respects_custom_icon_set_configuration()
    {
        config([
            'solar-icons.sets' => [
                'solar-linear',
                'solar-bold',
            ],
        ]);

        $provider = new SolarIconSetServiceProvider($this->app);
        $provider->boot();

        $iconFactory = $this->app->make(Factory::class);
        $registeredSets = $iconFactory->all();
        $setNames = array_keys($registeredSets);

        // Should have only the configured sets
        $this->assertTrue(in_array('solar-linear', $setNames), 'solar-linear should be registered');
        $this->assertTrue(in_array('solar-bold', $setNames), 'solar-bold should be registered');
        $this->assertTrue(in_array('default', $setNames), 'default combined set should be registered');

        // Should not have sets that weren't configured
        $this->assertFalse(in_array('solar-outline', $setNames), 'solar-outline should not be registered when not configured');
        $this->assertFalse(in_array('solar-broken', $setNames), 'solar-broken should not be registered when not configured');
    }

    /** @test */
    public function it_handles_empty_icon_sets_configuration_gracefully()
    {
        config([
            'solar-icons.sets' => [],
        ]);

        $provider = new SolarIconSetServiceProvider($this->app);

        // Should not throw an exception
        $provider->boot();

        $iconFactory = $this->app->make(Factory::class);
        $registeredSets = $iconFactory->all();
        $setNames = array_keys($registeredSets);

        // Should still register the default set even with empty configuration
        $this->assertTrue(in_array('default', $setNames), 'Default set should still be registered with empty sets config');

        // Should not have any individual sets
        $this->assertFalse(in_array('solar-linear', $setNames), 'Individual sets should not be registered with empty config');
    }

    /** @test */
    public function it_applies_default_css_classes_and_attributes()
    {
        config([
            'solar-icons.class' => 'custom-icon-class',
            'solar-icons.attributes' => [
                'width' => 24,
                'height' => 24,
            ],
            'solar-icons.sets' => ['solar-linear'],
        ]);

        $provider = new SolarIconSetServiceProvider($this->app);
        $provider->boot();

        $iconFactory = $this->app->make(Factory::class);
        $registeredSets = $iconFactory->all();

        // Check that the solar-linear set was registered with correct configuration
        $this->assertArrayHasKey('solar-linear', $registeredSets);

        $solarLinearConfig = $registeredSets['solar-linear'];
        $this->assertEquals('custom-icon-class', $solarLinearConfig['class']);
        $this->assertEquals(['width' => 24, 'height' => 24], $solarLinearConfig['attributes']);
    }
}
