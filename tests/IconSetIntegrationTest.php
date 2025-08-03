<?php

use BladeUI\Icons\Factory;
use Illuminate\Support\Facades\File;

describe('Icon Set Integration', function () {

    describe('Icon Path Resolution', function () {
        it('resolves icon paths correctly for each set', function () {
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
                $iconSet = $iconFactory->all()[$prefix] ?? null;
                expect($iconSet)->not->toBeNull("Icon set {$prefix} should be registered");

                $path = $iconSet['paths'][0];
                expect(File::isDirectory($path))->toBeTrue("Path {$path} should be a valid directory");
                expect(str_contains($path, $prefix))->toBeTrue("Path should contain the prefix {$prefix}");
            }
        });

        it('can find actual SVG files in each icon set', function () {
            $iconSets = ['solar-bold', 'solar-linear']; // Test subset for performance
            $iconFactory = $this->app->make(Factory::class);

            foreach ($iconSets as $prefix) {
                $iconSet = $iconFactory->all()[$prefix];
                $iconPath = $iconSet['paths'][0];

                $svgFiles = collect(File::allFiles($iconPath))
                    ->filter(fn($file) => $file->getExtension() === 'svg')
                    ->take(5); // Just check first 5 files for performance

                expect($svgFiles->count())->toBeGreaterThan(0, "Should find SVG files in {$prefix}");

                foreach ($svgFiles as $svgFile) {
                    expect($svgFile->isFile())->toBeTrue();
                    expect($svgFile->getExtension())->toBe('svg');
                }
            }
        });
    });

    describe('Icon Set Configuration', function () {
        it('has correct configuration structure for each icon set', function () {
            $requiredKeys = ['paths', 'prefix'];
            $iconFactory = $this->app->make(Factory::class);
            $iconSets = $iconFactory->all();

            $solarSets = collect($iconSets)->filter(fn($set, $key) => str_starts_with($key, 'solar-'));

            expect($solarSets->count())->toBe(6, 'Should have exactly 6 solar icon sets');

            foreach ($solarSets as $prefix => $config) {
                foreach ($requiredKeys as $key) {
                    expect(array_key_exists($key, $config))->toBeTrue("Icon set {$prefix} should have {$key} configuration");
                }

                expect($config['prefix'])->toBe($prefix, "Prefix should match the key");
                expect(is_array($config['paths']))->toBeTrue("Paths should be an array");
                expect(count($config['paths']))->toBeGreaterThan(0, "Paths should not be empty");
            }
        });

        it('has unique prefixes for each icon set', function () {
            $iconFactory = $this->app->make(Factory::class);
            $iconSets = $iconFactory->all();
            $solarSets = collect($iconSets)->filter(fn($set, $key) => str_starts_with($key, 'solar-'));

            $prefixes = $solarSets->pluck('prefix')->toArray();
            $uniquePrefixes = array_unique($prefixes);

            expect(count($prefixes))->toBe(count($uniquePrefixes), 'All prefixes should be unique');
        });

        it('has valid paths for each icon set', function () {
            $iconFactory = $this->app->make(Factory::class);
            $iconSets = $iconFactory->all();
            $solarSets = collect($iconSets)->filter(fn($set, $key) => str_starts_with($key, 'solar-'));

            foreach ($solarSets as $prefix => $config) {
                $path = $config['paths'][0];

                expect(is_string($path))->toBeTrue("Path for {$prefix} should be a string");
                expect(File::isDirectory($path))->toBeTrue("Path for {$prefix} should be a valid directory: {$path}");
                expect(is_readable($path))->toBeTrue("Path for {$prefix} should be readable");
            }
        });
    });

    describe('Icon Content Validation', function () {
        it('validates SVG file structure in sample icons', function () {
            $iconFactory = $this->app->make(Factory::class);
            $iconSet = $iconFactory->all()['solar-bold'];
            $iconPath = $iconSet['paths'][0];

            $svgFiles = collect(File::allFiles($iconPath))
                ->filter(fn($file) => $file->getExtension() === 'svg')
                ->take(3); // Test just a few files for performance

            foreach ($svgFiles as $svgFile) {
                $content = File::get($svgFile->getPathname());

                expect(str_contains($content, '<svg'))->toBeTrue("File should contain SVG opening tag");
                expect(str_contains($content, '</svg>'))->toBeTrue("File should contain SVG closing tag");
                expect(str_contains($content, 'viewBox'))->toBeTrue("SVG should have viewBox attribute");
            }
        });

        it('ensures SVG files have proper XML structure', function () {
            $iconFactory = $this->app->make(Factory::class);
            $iconSet = $iconFactory->all()['solar-linear'];
            $iconPath = $iconSet['paths'][0];

            $svgFiles = collect(File::allFiles($iconPath))
                ->filter(fn($file) => $file->getExtension() === 'svg')
                ->take(2); // Test just a couple for performance

            foreach ($svgFiles as $svgFile) {
                $content = File::get($svgFile->getPathname());

                // Basic XML validation
                $previousUseInternalErrors = libxml_use_internal_errors(true);
                $doc = simplexml_load_string($content);
                $errors = libxml_get_errors();
                libxml_use_internal_errors($previousUseInternalErrors);

                expect($doc)->not->toBeFalse("SVG file should be valid XML: " . $svgFile->getFilename());
                expect(empty($errors))->toBeTrue("SVG file should have no XML errors: " . $svgFile->getFilename());
            }
        });
    });

    describe('Performance Considerations', function () {
        it('registers icon sets efficiently', function () {
            $startTime = microtime(true);

            // Test performance of accessing already registered icons
            $iconFactory = $this->app->make(Factory::class);
            $allSets = $iconFactory->all();
            $solarSets = collect($allSets)->filter(fn($set, $key) => str_starts_with($key, 'solar-'));

            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;

            expect($solarSets->count())->toBe(6);
            expect($executionTime)->toBeLessThan(1.0, 'Icon access should be fast');
        });

        it('handles large number of icon files efficiently', function () {
            $iconFactory = $this->app->make(Factory::class);
            $iconSet = $iconFactory->all()['solar-bold'];
            $iconPath = $iconSet['paths'][0];

            $startTime = microtime(true);
            $allFiles = File::allFiles($iconPath);
            $endTime = microtime(true);

            $executionTime = $endTime - $startTime;

            expect(count($allFiles))->toBeGreaterThan(0);
            expect($executionTime)->toBeLessThan(2.0, 'File enumeration should be reasonably fast');
        });
    });
});
