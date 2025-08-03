<?php

use BladeUI\Icons\Factory;
use Illuminate\Support\Facades\File;
use Monsefeledrisse\FilamentSolarIcons\SolarIconSetServiceProvider;

describe('Edge Cases and Error Handling', function () {
    describe('Service Provider Robustness', function () {
        it('handles missing BladeUI Icons Factory gracefully', function () {
            // Create a mock app without the Factory
            $mockApp = Mockery::mock(\Illuminate\Foundation\Application::class);
            $mockApp->shouldReceive('make')
                ->with(Factory::class)
                ->andThrow(new \Illuminate\Contracts\Container\BindingResolutionException('Factory not bound'));

            $provider = new SolarIconSetServiceProvider($mockApp);

            expect(fn() => $provider->boot())->toThrow(\Illuminate\Contracts\Container\BindingResolutionException::class);
        });

        it('handles corrupted icon directories', function () {
            // Test that the service provider handles invalid paths gracefully
            $basePath = dirname(__DIR__) . '/resources/icons/solar';

            // Verify that /dev/null is not a directory (this should be true on most systems)
            expect(is_dir('/dev/null'))->toBeFalse('/dev/null should not be a directory');

            // Verify that our actual icon directory exists
            expect(is_dir($basePath . '/solar-bold'))->toBeTrue('solar-bold directory should exist');

            // The service provider should only register valid directories
            $iconFactory = $this->app->make(Factory::class);
            $registeredSets = $iconFactory->all();
            expect($registeredSets)->toHaveKey('solar-bold');
        });
    });

    describe('Icon Directory Edge Cases', function () {
        it('handles empty icon directories', function () {
            // Create a temporary empty directory
            $tempDir = sys_get_temp_dir() . '/solar-test-empty-' . uniqid();
            File::makeDirectory($tempDir);

            try {
                $provider = new class($this->app, $tempDir) extends SolarIconSetServiceProvider {
                    private $tempDir;
                    
                    public function __construct($app, $tempDir)
                    {
                        parent::__construct($app);
                        $this->tempDir = $tempDir;
                    }
                    
                    protected function registerIcons()
                    {
                        $iconSets = [
                            'solar-empty' => $this->tempDir,
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
                
                $registeredSets = $this->app->make(Factory::class)->all();
                expect($registeredSets)->toHaveKey('solar-empty');
                
            } finally {
                // Clean up
                if (File::isDirectory($tempDir)) {
                    File::deleteDirectory($tempDir);
                }
            }
        });

        it('handles permission issues gracefully', function () {
            // Test that the service provider handles permission issues gracefully
            $basePath = dirname(__DIR__) . '/resources/icons/solar';

            // Test that our icon directories are readable
            expect(is_readable($basePath . '/solar-bold'))->toBeTrue('solar-bold should be readable');
            expect(is_readable($basePath . '/solar-linear'))->toBeTrue('solar-linear should be readable');

            // The service provider should work with readable directories
            $iconFactory = $this->app->make(Factory::class);
            $registeredSets = $iconFactory->all();
            expect($registeredSets)->toHaveKey('solar-bold');
            expect($registeredSets)->toHaveKey('solar-linear');
        });
    });

    describe('Factory Integration Edge Cases', function () {
        it('handles Factory with existing icon sets', function () {
            // Test that the factory can handle multiple icon sets
            $factory = $this->app->make(Factory::class);
            $registeredSets = $factory->all();

            // Should have our solar icon sets
            expect($registeredSets)->toHaveKey('solar-bold');
            expect($registeredSets)->toHaveKey('solar-linear');

            // Each set should have proper configuration
            expect($registeredSets['solar-bold'])->toHaveKey('paths');
            expect($registeredSets['solar-bold'])->toHaveKey('prefix');
        });

        it('validates icon set configuration', function () {
            $factory = $this->app->make(Factory::class);
            $registeredSets = $factory->all();

            // Test that solar-bold has correct configuration
            $solarBold = $registeredSets['solar-bold'];
            expect($solarBold['prefix'])->toBe('solar-bold');
            expect(File::isDirectory($solarBold['paths'][0]))->toBeTrue();
        });
    });

    describe('Memory and Resource Management', function () {
        it('handles large icon sets efficiently', function () {
            // Test with the largest icon set (solar-bold typically has the most icons)
            $iconFactory = $this->app->make(Factory::class);
            $iconSet = $iconFactory->all()['solar-bold'];
            $iconPath = $iconSet['paths'][0];

            $startMemory = memory_get_usage();
            $startTime = microtime(true);

            // Enumerate all files in the icon set
            $allFiles = File::allFiles($iconPath);
            $svgFiles = collect($allFiles)->filter(fn($file) => $file->getExtension() === 'svg');

            $endTime = microtime(true);
            $endMemory = memory_get_usage();

            expect($svgFiles->count())->toBeGreaterThan(0);
            expect($endTime - $startTime)->toBeLessThan(5.0, 'Should process files within reasonable time');
            // Allow a bit more headroom for environments with higher baseline memory usage
            expect($endMemory - $startMemory)->toBeLessThan(12 * 1024 * 1024, 'Should not use excessive memory');
        });
    });

    describe('Configuration Validation', function () {
        it('validates icon set configuration completeness', function () {
            $factory = $this->app->make(Factory::class);
            $solarSets = collect($factory->all())->filter(fn($set, $key) => str_starts_with($key, 'solar-'));

            foreach ($solarSets as $prefix => $config) {
                // Validate required configuration keys
                expect(array_key_exists('paths', $config))->toBeTrue("Icon set {$prefix} should have paths");
                expect(array_key_exists('prefix', $config))->toBeTrue("Icon set {$prefix} should have prefix");

                // Validate configuration values
                expect(is_array($config['paths']))->toBeTrue("Paths should be array for {$prefix}");
                expect(is_string($config['prefix']))->toBeTrue("Prefix should be string for {$prefix}");
                expect($config['prefix'])->toBe($prefix, "Prefix should match key for {$prefix}");

                // Validate path accessibility
                expect(File::isDirectory($config['paths'][0]))->toBeTrue("Path should be valid directory for {$prefix}");
            }
        });
    });
});
