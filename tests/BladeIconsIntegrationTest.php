<?php

declare(strict_types=1);

use BladeUI\Icons\Factory;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Monsefeledrisse\FilamentSolarIcons\SolarIcon;

describe('Blade Icons Integration', function () {
    beforeEach(function () {
        // Ensure the service provider is loaded
        $this->app->register(\Monsefeledrisse\FilamentSolarIcons\SolarIconSetServiceProvider::class);
    });

    describe('Icon Factory Registration', function () {
        it('registers Solar icon sets with BladeUI Icons Factory', function () {
            $factory = $this->app->make(Factory::class);
            $registeredSets = $factory->all();

            // Check that Solar icon sets are registered
            $expectedSets = ['solar-bold', 'solar-outline', 'solar-linear', 'solar-broken'];
            
            foreach ($expectedSets as $setName) {
                expect($registeredSets)->toHaveKey($setName);
                expect($registeredSets[$setName])->toHaveKey('paths');
                expect($registeredSets[$setName])->toHaveKey('prefix');
                expect($registeredSets[$setName]['prefix'])->toBe($setName);
            }
        });

        it('can resolve specific Solar icons from the factory', function () {
            $factory = $this->app->make(Factory::class);
            
            // Test that we can find specific icons
            $testIcons = [
                'solar-bold-facemask_circle',
                'solar-linear-facemask_circle',
                'solar-outline-facemask_circle'
            ];

            foreach ($testIcons as $iconName) {
                $parts = explode('-', $iconName, 3);
                $setName = $parts[0] . '-' . $parts[1]; // e.g., 'solar-bold'
                $iconFile = $parts[2]; // e.g., 'Home'
                
                if (isset($factory->all()[$setName])) {
                    $iconSet = $factory->all()[$setName];
                    $paths = $iconSet['paths'] ?? [];
                    
                    // Check if any of the paths contain the icon file
                    $iconExists = false;
                    foreach ($paths as $path) {
                        if (file_exists($path . '/' . strtolower($iconFile) . '.svg') ||
                            file_exists($path . '/' . $iconFile . '.svg')) {
                            $iconExists = true;
                            break;
                        }
                    }
                    
                    expect($iconExists)->toBeTrue("Icon {$iconName} should exist in the file system");
                }
            }
        });
    });

    describe('Blade Component Integration', function () {
        it('can render Solar icons using x-icon component', function () {
            // Test basic icon rendering with actual file names
            $iconName = 'solar-bold-facemask_circle';
            $html = Blade::render('<x-icon name="' . $iconName . '" />');

            expect($html)->toContain('<svg');
            expect($html)->toContain('</svg>');
        });

        it('can render Solar icons with custom attributes', function () {
            $iconName = 'solar-linear-facemask_circle';
            $html = Blade::render('<x-icon name="' . $iconName . '" class="w-6 h-6 text-blue-500" />');

            expect($html)->toContain('<svg');
            expect($html)->toContain('class="w-6 h-6 text-blue-500"');
            expect($html)->toContain('</svg>');
        });

        it('handles different Solar icon styles', function () {
            $iconStyles = [
                'solar-bold-facemask_circle',
                'solar-outline-facemask_circle',
                'solar-linear-facemask_circle',
                'solar-broken-facemask_circle'
            ];

            foreach ($iconStyles as $iconName) {
                $html = Blade::render('<x-icon name="' . $iconName . '" />');
                
                expect($html)->toContain('<svg');
                expect($html)->toContain('</svg>');
            }
        });
    });

    describe('SVG Directive Integration', function () {
        it('can render Solar icons using @svg directive', function () {
            $iconName = 'solar-bold-facemask_circle';
            $template = "@svg('{$iconName}')";
            $html = Blade::render($template);

            expect($html)->toContain('<svg');
            expect($html)->toContain('</svg>');
        });

        it('can render Solar icons with attributes using @svg directive', function () {
            $iconName = 'solar-linear-facemask_circle';
            $template = "@svg('{$iconName}', 'w-8 h-8 text-green-600')";
            $html = Blade::render($template);

            expect($html)->toContain('<svg');
            expect($html)->toContain('class="w-8 h-8 text-green-600"');
            expect($html)->toContain('</svg>');
        });

        it('can render Solar icons with array attributes using @svg directive', function () {
            $iconName = 'solar-outline-facemask_circle';
            $template = "@svg('{$iconName}', ['class' => 'icon-star', 'data-testid' => 'star-icon'])";
            $html = Blade::render($template);

            expect($html)->toContain('<svg');
            expect($html)->toContain('class="icon-star"');
            expect($html)->toContain('data-testid="star-icon"');
            expect($html)->toContain('</svg>');
        });
    });

    describe('Enum Integration', function () {
        it('can use SolarIcon enum values with Blade components', function () {
            $enumIcon = SolarIcon::FacemaskCircle;
            $html = Blade::render('<x-icon name="' . $enumIcon->value . '" />');
            
            expect($html)->toContain('<svg');
            expect($html)->toContain('</svg>');
        });

        it('can use SolarIcon enum with @svg directive', function () {
            $enumIcon = SolarIcon::FacemaskCircle;
            $template = "@svg('{$enumIcon->value}')";
            $html = Blade::render($template);

            expect($html)->toContain('<svg');
            expect($html)->toContain('</svg>');
        });

        it('validates that enum values correspond to actual icons', function () {
            // Test a few random enum cases
            $testCases = [
                SolarIcon::FacemaskCircle,
                SolarIcon::ConfoundedCircle,
                SolarIcon::SadSquare,
            ];

            foreach ($testCases as $enumCase) {
                $iconName = $enumCase->value;
                
                // Verify the enum value format (now with underscores)
                expect($iconName)->toMatch('/^solar-[a-z-]+-[a-z0-9_]+$/');

                // Try to render it
                $html = Blade::render('<x-icon name="' . $iconName . '" />');
                expect($html)->toContain('<svg');
            }
        });
    });

    describe('Error Handling', function () {
        it('handles non-existent Solar icons gracefully', function () {
            $nonExistentIcon = 'solar-bold-NonExistentIcon123';
            
            // This should not throw an exception but may return empty or error content
            $html = Blade::render('<x-icon name="' . $nonExistentIcon . '" />');
            
            // The exact behavior depends on BladeUI Icons configuration
            // but it should not crash the application
            expect($html)->toBeString();
        });

        it('handles malformed icon names gracefully', function () {
            $malformedIcons = [
                'not-a-solar-icon',
                'solar-invalid',
                '',
                'solar--Home',
            ];

            foreach ($malformedIcons as $iconName) {
                $html = Blade::render('<x-icon name="' . $iconName . '" />');
                expect($html)->toBeString();
            }
        });
    });

    describe('Performance and Caching', function () {
        it('can render multiple icons efficiently', function () {
            $icons = [
                'solar-bold-facemask_circle',
                'solar-linear-facemask_circle',
                'solar-outline-facemask_circle',
                'solar-broken-facemask_circle',
                'solar-bold-confounded_circle',
            ];

            $startTime = microtime(true);
            
            foreach ($icons as $iconName) {
                $html = Blade::render('<x-icon name="' . $iconName . '" />');
                expect($html)->toContain('<svg');
            }
            
            $endTime = microtime(true);
            $duration = $endTime - $startTime;
            
            // Should render 5 icons in less than 1 second
            expect($duration)->toBeLessThan(1.0);
        });
    });

    describe('Icon Content Validation', function () {
        it('renders valid SVG content', function () {
            $iconName = 'solar-bold-facemask_circle';
            $html = Blade::render('<x-icon name="' . $iconName . '" />');

            // Basic SVG structure validation
            expect($html)->toContain('<svg');
            expect($html)->toContain('</svg>');
            expect($html)->toContain('viewBox');

            // Should not contain PHP errors or warnings
            expect($html)->not->toContain('<?php');
            expect($html)->not->toContain('Warning:');
            expect($html)->not->toContain('Error:');
        });

        it('preserves SVG attributes and structure', function () {
            $iconName = 'solar-linear-facemask_circle';
            $html = Blade::render('<x-icon name="' . $iconName . '" />');

            // Check for common SVG attributes
            expect($html)->toMatch('/<svg[^>]*viewBox="[^"]*"/');
            expect($html)->toMatch('/<svg[^>]*xmlns="[^"]*"/');

            // Should contain path or other SVG elements
            $containsSvgElements = str_contains($html, '<path') ||
                                 str_contains($html, '<circle') ||
                                 str_contains($html, '<rect') ||
                                 str_contains($html, '<g');

            expect($containsSvgElements)->toBeTrue('SVG should contain drawing elements');
        });
    });
});
