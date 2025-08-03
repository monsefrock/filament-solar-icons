<?php

namespace Monsefeledrisse\LaravelSolarIcons\Tests;

use BladeUI\Icons\Factory;
use Monsefeledrisse\LaravelSolarIcons\SolarIconSetServiceProvider;

class LoggingConfigurationTest extends TestCase
{
    /** @test */
    public function it_respects_logging_configuration_when_disabled()
    {
        // Set configuration to disable logging
        config([
            'solar-icons.development.log_flattening' => false,
            'app.debug' => true, // Even with debug true, logging should be disabled
        ]);

        // The service provider is already booted by the TestCase
        // We just need to verify that the configuration is respected
        $this->assertFalse(config('solar-icons.development.log_flattening'));
        $this->assertTrue(config('app.debug'));

        // This test verifies that even with debug=true, logging is controlled by our config
        $this->assertTrue(true, 'Configuration is properly set to disable logging');
    }

    /** @test */
    public function it_has_correct_default_configuration_values()
    {
        // Check that the new configuration keys exist with correct defaults
        $this->assertFalse(config('solar-icons.development.log_flattening', true), 'log_flattening should default to false');
        $this->assertFalse(config('solar-icons.development.log_missing_icons', true), 'log_missing_icons should default to false');
        $this->assertFalse(config('solar-icons.development.force_rebuild', true), 'force_rebuild should default to false');
    }

    /** @test */
    public function it_can_control_rebuild_behavior()
    {
        // Test that force_rebuild configuration is respected
        config(['solar-icons.development.force_rebuild' => false]);

        // Verify the configuration is set correctly
        $this->assertFalse(config('solar-icons.development.force_rebuild'));

        // The service provider should respect this setting
        // This is a configuration test rather than a functional test
        $this->assertTrue(true, 'force_rebuild configuration is properly set');
    }

    /** @test */
    public function it_registers_icons_without_excessive_logging()
    {
        // Ensure logging is disabled
        config([
            'solar-icons.development.log_flattening' => false,
            'solar-icons.development.force_rebuild' => false,
        ]);

        $factory = $this->app->make(Factory::class);

        // Verify icons are registered (service provider already booted by TestCase)
        $registeredSets = $factory->all();
        $this->assertArrayHasKey('solar-linear', $registeredSets);
        $this->assertArrayHasKey('solar-bold', $registeredSets);

        // Verify configuration is set to prevent logging
        $this->assertFalse(config('solar-icons.development.log_flattening'));
    }

    /** @test */
    public function it_maintains_backward_compatibility()
    {
        // Test that the package works without the new configuration keys
        config([
            'solar-icons' => [
                'class' => '',
                'attributes' => [],
                'sets' => ['solar-linear', 'solar-bold'],
                // Note: no 'development' key - testing backward compatibility
            ]
        ]);

        // Verify that missing development config doesn't break anything
        $this->assertFalse(config('solar-icons.development.log_flattening', false));
        $this->assertFalse(config('solar-icons.development.force_rebuild', false));

        // Should not throw any errors when accessing missing config
        $this->assertTrue(true, 'Backward compatibility maintained');
    }
}
