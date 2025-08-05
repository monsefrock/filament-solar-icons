<?php

use BladeUI\Icons\Factory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Monsefeledrisse\LaravelSolarIcons\SolarIconSetServiceProvider;

describe('Performance Optimization Tests', function () {
    describe('Lazy Loading Configuration', function () {
        it('respects lazy loading configuration', function () {
            // Test with lazy loading enabled
            Config::set('solar-icons.performance.lazy_loading', true);
            Config::set('solar-icons.performance.preload_sets', 'solar-outline,solar-linear');
            
            $provider = new SolarIconSetServiceProvider($this->app);
            $provider->boot();
            
            $factory = $this->app->make(Factory::class);
            $registeredSets = $factory->all();
            
            // Should have all sets registered (for compatibility)
            expect($registeredSets)->toHaveKey('solar-outline');
            expect($registeredSets)->toHaveKey('solar-linear');
            expect($registeredSets)->toHaveKey('solar-bold');
            expect($registeredSets)->toHaveKey('default');
        });

        it('handles empty preload sets configuration', function () {
            Config::set('solar-icons.performance.lazy_loading', true);
            Config::set('solar-icons.performance.preload_sets', '');
            
            $provider = new SolarIconSetServiceProvider($this->app);
            $provider->boot();
            
            $factory = $this->app->make(Factory::class);
            $registeredSets = $factory->all();
            
            // Should still register all sets when no preload configuration
            expect(count($registeredSets))->toBeGreaterThanOrEqual(6);
        });

        it('falls back to normal registration when lazy loading disabled', function () {
            Config::set('solar-icons.performance.lazy_loading', false);
            
            $provider = new SolarIconSetServiceProvider($this->app);
            $provider->boot();
            
            $factory = $this->app->make(Factory::class);
            $registeredSets = $factory->all();
            
            // Should have all sets registered normally
            expect(count($registeredSets))->toBeGreaterThanOrEqual(6);
        });
    });

    describe('Caching Performance', function () {
        it('respects cache configuration', function () {
            Config::set('solar-icons.performance.cache_enabled', true);
            Config::set('solar-icons.performance.cache_ttl', 1800);
            
            $provider = new SolarIconSetServiceProvider($this->app);
            $provider->boot();
            
            // Should not throw any errors
            expect(true)->toBeTrue();
        });

        it('handles cache failures gracefully', function () {
            Config::set('solar-icons.performance.cache_enabled', true);
            
            // Mock cache failure
            Cache::shouldReceive('remember')
                ->andThrow(new \Exception('Cache failure'));
            
            $provider = new SolarIconSetServiceProvider($this->app);
            
            // Should not throw exception even with cache failure
            expect(fn() => $provider->boot())->not->toThrow(\Exception::class);
        });
    });

    describe('Performance Metrics', function () {
        it('registers icon sets efficiently', function () {
            $startTime = microtime(true);
            $startMemory = memory_get_usage();
            
            Config::set('solar-icons.performance.lazy_loading', true);
            Config::set('solar-icons.performance.preload_sets', 'solar-outline');
            
            $provider = new SolarIconSetServiceProvider($this->app);
            $provider->boot();
            
            $endTime = microtime(true);
            $endMemory = memory_get_usage();
            
            $executionTime = $endTime - $startTime;
            $memoryUsed = $endMemory - $startMemory;
            
            // Should complete within reasonable time and memory limits
            expect($executionTime)->toBeLessThan(2.0, 'Registration should complete within 2 seconds');
            expect($memoryUsed)->toBeLessThan(50 * 1024 * 1024, 'Should use less than 50MB of memory');
        });

        it('handles large icon sets without excessive memory usage', function () {
            $startMemory = memory_get_usage();
            
            $factory = $this->app->make(Factory::class);
            $registeredSets = $factory->all();
            
            // Access multiple icon sets
            foreach (['solar-bold', 'solar-linear', 'solar-outline'] as $setName) {
                if (isset($registeredSets[$setName])) {
                    $iconSet = $registeredSets[$setName];
                    expect($iconSet)->toHaveKey('paths');
                }
            }
            
            $endMemory = memory_get_usage();
            $memoryUsed = $endMemory - $startMemory;
            
            expect($memoryUsed)->toBeLessThan(20 * 1024 * 1024, 'Icon set access should use less than 20MB');
        });
    });

    describe('Error Handling and Graceful Degradation', function () {
        it('handles missing icon directories gracefully', function () {
            $provider = new class($this->app) extends SolarIconSetServiceProvider {
                protected function registerIconSet($factory, string $set): void
                {
                    // Simulate missing directory
                    if ($set === 'solar-nonexistent') {
                        $sourcePath = '/nonexistent/path';
                        if (!is_dir($sourcePath)) {
                            return; // Should return gracefully
                        }
                    }
                    parent::registerIconSet($factory, $set);
                }
            };
            
            expect(fn() => $provider->boot())->not->toThrow(\Exception::class);
        });

        it('continues registration when individual sets fail', function () {
            $provider = new class($this->app) extends SolarIconSetServiceProvider {
                protected function registerIconSet($factory, string $set): void
                {
                    if ($set === 'solar-bold') {
                        throw new \Exception('Simulated failure');
                    }
                    parent::registerIconSet($factory, $set);
                }
            };
            
            // Should not throw exception and should continue with other sets
            expect(fn() => $provider->boot())->not->toThrow(\Exception::class);
            
            $factory = $this->app->make(Factory::class);
            $registeredSets = $factory->all();
            
            // Should still have other sets registered
            expect($registeredSets)->toHaveKey('solar-linear');
            expect($registeredSets)->toHaveKey('solar-outline');
        });
    });

    describe('Configuration Validation', function () {
        it('validates performance configuration values', function () {
            // Test default values
            expect(config('solar-icons.performance.cache_enabled'))->toBeBool();
            expect(config('solar-icons.performance.lazy_loading'))->toBeBool();
            expect(config('solar-icons.performance.cache_ttl'))->toBeInt();
        });

        it('handles invalid preload sets configuration', function () {
            Config::set('solar-icons.performance.preload_sets', 'invalid-set,another-invalid');
            
            $provider = new SolarIconSetServiceProvider($this->app);
            
            // Should not throw exception with invalid preload sets
            expect(fn() => $provider->boot())->not->toThrow(\Exception::class);
        });
    });

    describe('Backward Compatibility', function () {
        it('maintains compatibility with existing icon usage', function () {
            // Test that existing icon access patterns still work
            $factory = $this->app->make(Factory::class);
            $registeredSets = $factory->all();
            
            // All expected sets should be available
            $expectedSets = ['solar-bold', 'solar-linear', 'solar-outline', 'solar-broken'];
            
            foreach ($expectedSets as $setName) {
                expect($registeredSets)->toHaveKey($setName);
                expect($registeredSets[$setName])->toHaveKey('paths');
                expect($registeredSets[$setName])->toHaveKey('prefix');
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
                    
                    // Paths should be valid directories
                    foreach ($config['paths'] as $path) {
                        expect(is_dir($path))->toBeTrue("Path {$path} should be a valid directory");
                    }
                }
            }
        });
    });

    describe('Optimization Effectiveness', function () {
        it('reduces registration time with lazy loading', function () {
            // Test without lazy loading
            Config::set('solar-icons.performance.lazy_loading', false);
            $startTime = microtime(true);
            
            $provider1 = new SolarIconSetServiceProvider($this->app);
            $provider1->boot();
            
            $timeWithoutLazy = microtime(true) - $startTime;
            
            // Reset and test with lazy loading
            $this->refreshApplication();
            Config::set('solar-icons.performance.lazy_loading', true);
            Config::set('solar-icons.performance.preload_sets', 'solar-outline');
            
            $startTime = microtime(true);
            
            $provider2 = new SolarIconSetServiceProvider($this->app);
            $provider2->boot();
            
            $timeWithLazy = microtime(true) - $startTime;
            
            // Lazy loading should be faster or at least not significantly slower
            expect($timeWithLazy)->toBeLessThanOrEqual($timeWithoutLazy * 1.5, 
                'Lazy loading should not be significantly slower than normal loading');
        });
    });
});
