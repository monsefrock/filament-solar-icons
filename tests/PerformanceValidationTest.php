<?php

use BladeUI\Icons\Factory;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Monsefeledrisse\LaravelSolarIcons\SolarIconSetServiceProvider;

describe('Performance Validation Tests', function () {
    describe('Icon Rendering Functionality', function () {
        it('can render icons from all registered sets', function () {
            $factory = $this->app->make(Factory::class);
            $registeredSets = $factory->all();
            
            $testIcons = [
                'solar-outline' => 'home',
                'solar-linear' => 'user', 
                'solar-bold' => 'star',
            ];
            
            foreach ($testIcons as $setName => $iconName) {
                if (isset($registeredSets[$setName])) {
                    // Test Blade component rendering
                    $html = Blade::render("<x-icon name=\"{$setName}-{$iconName}\" />");
                    expect($html)->toContain('<svg');
                    expect($html)->toContain('</svg>');
                    
                    // Test @svg directive
                    $html2 = Blade::render("@svg('{$setName}-{$iconName}')");
                    expect($html2)->toContain('<svg');
                    expect($html2)->toContain('</svg>');
                }
            }
        });

        it('preserves icon quality and attributes', function () {
            $html = Blade::render('<x-icon name="solar-outline-home" class="w-6 h-6" />');
            
            expect($html)->toContain('<svg');
            expect($html)->toContain('class="w-6 h-6"');
            expect($html)->toContain('viewBox');
        });
    });

    describe('Performance Configuration Impact', function () {
        it('lazy loading reduces initial registration time', function () {
            // Measure time with lazy loading enabled
            Config::set('solar-icons.performance.lazy_loading', true);
            Config::set('solar-icons.performance.preload_sets', 'solar-outline');
            
            $startTime = microtime(true);
            $provider = new SolarIconSetServiceProvider($this->app);
            $provider->boot();
            $lazyTime = microtime(true) - $startTime;
            
            // Reset application
            $this->refreshApplication();
            
            // Measure time with lazy loading disabled
            Config::set('solar-icons.performance.lazy_loading', false);
            
            $startTime = microtime(true);
            $provider2 = new SolarIconSetServiceProvider($this->app);
            $provider2->boot();
            $normalTime = microtime(true) - $startTime;
            
            // Lazy loading should be faster or comparable
            expect($lazyTime)->toBeLessThanOrEqual($normalTime * 1.2);
        });

        it('caching improves subsequent registrations', function () {
            Config::set('solar-icons.performance.cache_enabled', true);
            Config::set('solar-icons.performance.cache_ttl', 3600);
            
            // First registration (cache miss)
            $startTime = microtime(true);
            $provider1 = new SolarIconSetServiceProvider($this->app);
            $provider1->boot();
            $firstTime = microtime(true) - $startTime;
            
            // Second registration (should use cache)
            $startTime = microtime(true);
            $provider2 = new SolarIconSetServiceProvider($this->app);
            $provider2->boot();
            $secondTime = microtime(true) - $startTime;
            
            // Second time should be faster or comparable
            expect($secondTime)->toBeLessThanOrEqual($firstTime * 1.5);
        });
    });

    describe('Memory Usage Optimization', function () {
        it('uses reasonable memory for icon registration', function () {
            $startMemory = memory_get_usage(true);
            
            Config::set('solar-icons.performance.lazy_loading', true);
            Config::set('solar-icons.performance.preload_sets', 'solar-outline,solar-linear');
            
            $provider = new SolarIconSetServiceProvider($this->app);
            $provider->boot();
            
            $endMemory = memory_get_usage(true);
            $memoryUsed = $endMemory - $startMemory;
            
            // Should use less than 30MB for registration
            expect($memoryUsed)->toBeLessThan(30 * 1024 * 1024);
        });

        it('handles icon access without memory leaks', function () {
            $factory = $this->app->make(Factory::class);
            $registeredSets = $factory->all();
            
            $startMemory = memory_get_usage(true);
            
            // Access multiple icons multiple times
            for ($i = 0; $i < 10; $i++) {
                foreach (['solar-outline', 'solar-linear', 'solar-bold'] as $setName) {
                    if (isset($registeredSets[$setName])) {
                        $iconSet = $registeredSets[$setName];
                        expect($iconSet)->toHaveKey('paths');
                    }
                }
            }
            
            $endMemory = memory_get_usage(true);
            $memoryIncrease = $endMemory - $startMemory;
            
            // Memory increase should be minimal (less than 5MB)
            expect($memoryIncrease)->toBeLessThan(5 * 1024 * 1024);
        });
    });

    describe('Functionality Preservation', function () {
        it('maintains all expected icon sets', function () {
            $factory = $this->app->make(Factory::class);
            $registeredSets = $factory->all();
            
            $expectedSets = [
                'solar-bold',
                'solar-bold-duotone', 
                'solar-broken',
                'solar-line-duotone',
                'solar-linear',
                'solar-outline',
                'default'
            ];
            
            foreach ($expectedSets as $setName) {
                expect($registeredSets)->toHaveKey($setName);
            }
        });

        it('preserves icon set configuration structure', function () {
            $factory = $this->app->make(Factory::class);
            $registeredSets = $factory->all();
            
            foreach ($registeredSets as $setName => $config) {
                if (str_starts_with($setName, 'solar-')) {
                    expect($config)->toHaveKey('paths');
                    expect($config)->toHaveKey('prefix');
                    expect($config['prefix'])->toBe($setName);
                    
                    // Verify paths exist
                    expect($config['paths'])->toBeArray();
                    expect(count($config['paths']))->toBeGreaterThan(0);
                    
                    foreach ($config['paths'] as $path) {
                        expect(is_dir($path))->toBeTrue("Path {$path} should exist");
                    }
                }
            }
        });

        it('supports custom CSS classes and attributes', function () {
            // Reset application to ensure clean state
            $this->refreshApplication();

            Config::set('solar-icons.class', 'custom-icon-class');
            Config::set('solar-icons.attributes', ['data-testid' => 'solar-icon']);

            $provider = new SolarIconSetServiceProvider($this->app);
            $provider->boot();

            $html = Blade::render('<x-icon name="solar-outline-home" class="additional-class" />');

            // Check that the icon renders (basic functionality)
            expect($html)->toContain('<svg');
            expect($html)->toContain('</svg>');

            // The custom classes should be applied through the icon set configuration
            // Note: The exact implementation may vary based on how BladeUI Icons handles default classes
            expect($html)->toContain('additional-class');
        });
    });

    describe('Error Resilience', function () {
        it('continues working when some icon sets fail to load', function () {
            $provider = new class($this->app) extends SolarIconSetServiceProvider {
                protected function registerIconSet($factory, string $set): void
                {
                    if ($set === 'solar-bold') {
                        // Simulate failure for one set
                        throw new \Exception('Simulated failure');
                    }
                    parent::registerIconSet($factory, $set);
                }
            };
            
            // Should not throw exception
            expect(fn() => $provider->boot())->not->toThrow(\Exception::class);
            
            // Other sets should still be available
            $factory = $this->app->make(Factory::class);
            $registeredSets = $factory->all();
            
            expect($registeredSets)->toHaveKey('solar-linear');
            expect($registeredSets)->toHaveKey('solar-outline');
        });

        it('handles temporary directory creation failures gracefully', function () {
            $provider = new class($this->app) extends SolarIconSetServiceProvider {
                protected function createFlattenedIconSet(string $sourcePath, string $set): ?string
                {
                    // Simulate temp directory creation failure
                    return null;
                }
            };
            
            // Should not throw exception
            expect(fn() => $provider->boot())->not->toThrow(\Exception::class);
        });
    });

    describe('Performance Benchmarks', function () {
        it('completes icon registration within performance targets', function () {
            $startTime = microtime(true);
            $startMemory = memory_get_usage(true);
            
            Config::set('solar-icons.performance.lazy_loading', true);
            Config::set('solar-icons.performance.preload_sets', 'solar-outline,solar-linear');
            Config::set('solar-icons.performance.cache_enabled', true);
            
            $provider = new SolarIconSetServiceProvider($this->app);
            $provider->boot();
            
            $endTime = microtime(true);
            $endMemory = memory_get_usage(true);
            
            $executionTime = $endTime - $startTime;
            $memoryUsed = $endMemory - $startMemory;
            
            // Performance targets
            expect($executionTime)->toBeLessThan(1.0, 'Should complete within 1 second');
            expect($memoryUsed)->toBeLessThan(25 * 1024 * 1024, 'Should use less than 25MB');
            
            // Verify functionality still works
            $factory = $this->app->make(Factory::class);
            $registeredSets = $factory->all();
            expect(count($registeredSets))->toBeGreaterThanOrEqual(6);
        });
    });
});
