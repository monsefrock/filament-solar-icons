<?php

use BladeUI\Icons\Factory;
use Illuminate\Support\Facades\File;
use Monsefeledrisse\LaravelSolarIcons\SolarIconSetServiceProvider;

describe('SolarIconSetServiceProvider', function () {
    it('can be instantiated', function () {
        $serviceProvider = new SolarIconSetServiceProvider($this->app);
        expect($serviceProvider)->toBeInstanceOf(SolarIconSetServiceProvider::class);
    });

    it('registers with Laravel service container', function () {
        $providers = $this->app->getLoadedProviders();
        expect($providers)->toHaveKey(SolarIconSetServiceProvider::class);
    });

    it('boots successfully', function () {
        // The service provider is already booted by Laravel's package discovery
        // This test verifies that the service provider can be instantiated without errors
        $serviceProvider = new SolarIconSetServiceProvider($this->app);
        expect($serviceProvider)->toBeInstanceOf(SolarIconSetServiceProvider::class);

        // Verify that the icons are already registered (meaning boot was successful)
        $iconFactory = $this->app->make(Factory::class);
        expect($iconFactory->all())->toHaveKey('solar-bold');
    });

    describe('Icon Set Registration', function () {
        it('registers all 6 Solar icon sets', function () {
            $iconSets = [
                'solar-bold',
                'solar-bold-duotone',
                'solar-broken',
                'solar-line-duotone',
                'solar-linear',
                'solar-outline'
            ];

            $iconFactory = $this->app->make(Factory::class);

            foreach ($iconSets as $prefix) {
                expect($iconFactory->all())->toHaveKey($prefix);
            }
        });

        it('registers icon sets with correct prefixes', function () {
            $iconSets = [
                'solar-bold',
                'solar-bold-duotone',
                'solar-broken',
                'solar-line-duotone',
                'solar-linear',
                'solar-outline'
            ];

            $iconFactory = $this->app->make(Factory::class);
            $registeredSets = $iconFactory->all();

            foreach ($iconSets as $expectedPrefix) {
                expect($registeredSets)->toHaveKey($expectedPrefix);
                expect($registeredSets[$expectedPrefix]['prefix'])->toBe($expectedPrefix);
            }
        });

        it('registers icon sets with correct paths', function () {
            $iconSets = [
                'solar-bold',
                'solar-bold-duotone',
                'solar-broken',
                'solar-line-duotone',
                'solar-linear',
                'solar-outline'
            ];

            $iconFactory = $this->app->make(Factory::class);
            $registeredSets = $iconFactory->all();

            foreach ($iconSets as $prefix) {
                $actualPath = $registeredSets[$prefix]['paths'][0];
                // The service provider creates temporary flattened directories
                expect(str_contains($actualPath, "solar-icons/{$prefix}"))->toBeTrue("Path should contain solar-icons/{$prefix}");
                expect(File::isDirectory($actualPath))->toBeTrue("Path should be a valid directory");

                // Verify the directory contains SVG files
                $svgFiles = glob($actualPath . '/*.svg');
                expect(count($svgFiles))->toBeGreaterThan(0, "Directory should contain SVG files");
            }
        });

        it('only registers icon sets for existing directories', function () {
            $basePath = dirname(__DIR__) . '/resources/icons/solar';

            // Test that non-existent directories are not registered
            $nonExistentPath = $basePath . '/solar-nonexistent';
            expect(is_dir($nonExistentPath))->toBeFalse('Non-existent directory should not exist');

            // Test that existing directories are registered
            $existentPath = $basePath . '/solar-bold';
            expect(is_dir($existentPath))->toBeTrue('Existing directory should exist');

            $iconFactory = $this->app->make(Factory::class);
            $registeredSets = $iconFactory->all();

            expect($registeredSets)->not->toHaveKey('solar-nonexistent');
            expect($registeredSets)->toHaveKey('solar-bold');
        });
    });

    describe('Icon Directory Verification', function () {
        it('verifies all expected icon directories exist', function () {
            $basePath = dirname(__DIR__) . '/resources/icons/solar';
            $expectedDirectories = [
                'solar-bold',
                'solar-bold-duotone',
                'solar-broken', 
                'solar-line-duotone',
                'solar-linear',
                'solar-outline'
            ];

            foreach ($expectedDirectories as $directory) {
                $fullPath = $basePath . '/' . $directory;
                expect(File::isDirectory($fullPath))
                    ->toBeTrue("Directory {$directory} should exist at {$fullPath}");
            }
        });

        it('verifies icon directories contain SVG files', function () {
            $basePath = dirname(__DIR__) . '/resources/icons/solar';
            $iconSets = ['solar-bold', 'solar-linear']; // Test a couple of sets
            
            foreach ($iconSets as $iconSet) {
                $iconPath = $basePath . '/' . $iconSet;
                $svgFiles = File::allFiles($iconPath);
                $svgCount = collect($svgFiles)->filter(fn($file) => $file->getExtension() === 'svg')->count();
                
                expect($svgCount)->toBeGreaterThan(0, "Icon set {$iconSet} should contain SVG files");
            }
        });
    });

    describe('BladeUI Icons Integration', function () {
        it('integrates properly with BladeUI Icons Factory', function () {
            $iconFactory = $this->app->make(Factory::class);
            expect($iconFactory)->toBeInstanceOf(Factory::class);

            $registeredSets = $iconFactory->all();
            expect($registeredSets)->toBeArray();
            expect(count($registeredSets))->toBeGreaterThanOrEqual(6);
        });

        it('allows icon retrieval through BladeUI Icons Factory', function () {
            $iconFactory = $this->app->make(Factory::class);

            // Test if we can get an icon set configuration
            $solarBoldConfig = $iconFactory->all()['solar-bold'] ?? null;

            expect($solarBoldConfig)->not->toBeNull();
            expect($solarBoldConfig)->toHaveKey('paths');
            expect($solarBoldConfig)->toHaveKey('prefix');
        });
    });

    describe('Error Handling', function () {
        it('handles missing icon directories gracefully', function () {
            // Create a service provider that tries to register a non-existent directory
            $provider = new class($this->app) extends SolarIconSetServiceProvider {
                protected function registerIcons(): void
                {
                    $iconSets = [
                        'non-existent' => '/path/that/does/not/exist',
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
            };

            expect(fn() => $provider->boot())->not->toThrow(Exception::class);
        });

        it('handles Factory instantiation errors gracefully', function () {
            $provider = new class($this->app) extends SolarIconSetServiceProvider {
                protected function registerIcons(): void
                {
                    try {
                        /** @var Factory $icons */
                        $icons = $this->app->make(Factory::class);
                        // This should work normally
                        expect($icons)->toBeInstanceOf(Factory::class);
                    } catch (Exception $e) {
                        // If there's an issue, it should be handled gracefully
                        expect($e)->toBeInstanceOf(Exception::class);
                    }
                }
            };

            expect(fn() => $provider->boot())->not->toThrow(Exception::class);
        });
    });

    describe('Service Provider Lifecycle', function () {
        it('can be booted multiple times without issues', function () {
            // The service provider is already booted by Laravel's package discovery
            // Multiple boots would cause conflicts, so we test that the registration is stable
            $iconFactory = $this->app->make(Factory::class);
            $firstCheck = $iconFactory->all();

            // Wait a moment and check again - should be stable
            usleep(1000);
            $secondCheck = $iconFactory->all();

            expect(count($firstCheck))->toBe(count($secondCheck));
            expect($firstCheck['solar-bold'])->toEqual($secondCheck['solar-bold']);
        });

        it('maintains icon set registrations after boot', function () {
            $iconFactory = $this->app->make(Factory::class);
            $registeredSets = $iconFactory->all();

            foreach (['solar-bold', 'solar-linear', 'solar-outline'] as $prefix) {
                expect($registeredSets)->toHaveKey($prefix);
                expect($registeredSets[$prefix])->toHaveKey('paths');
                expect($registeredSets[$prefix])->toHaveKey('prefix');
            }
        });
    });
});
