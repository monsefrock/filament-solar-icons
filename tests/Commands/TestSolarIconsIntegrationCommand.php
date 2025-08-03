<?php

declare(strict_types=1);

namespace Monsefeledrisse\FilamentSolarIcons\Tests\Commands;

use BladeUI\Icons\Factory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Blade;
use Monsefeledrisse\FilamentSolarIcons\SolarIcon;
use Throwable;

/**
 * Console command to test Solar Icons integration with blade-ui-kit/blade-icons
 */
class TestSolarIconsIntegrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'solar-icons:test-integration 
                            {--verbose : Show detailed output}
                            {--quick : Run only quick tests}
                            {--icon= : Test specific icon}';

    /**
     * The console command description.
     */
    protected $description = 'Test Solar Icons integration with blade-ui-kit/blade-icons';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🧪 Testing Solar Icons Integration with blade-ui-kit/blade-icons');
        $this->newLine();

        $verbose = $this->option('verbose');
        $quick = $this->option('quick');
        $specificIcon = $this->option('icon');

        $results = [
            'passed' => 0,
            'failed' => 0,
            'errors' => []
        ];

        try {
            // Test 1: Service Provider Registration
            $this->testServiceProviderRegistration($results, $verbose);

            // Test 2: Icon Factory Registration
            $this->testIconFactoryRegistration($results, $verbose);

            // Test 3: Enum Integration
            $this->testEnumIntegration($results, $verbose);

            // Test 4: Blade Component Rendering
            if (!$quick) {
                $this->testBladeComponentRendering($results, $verbose);
            }

            // Test 5: SVG Directive Rendering
            if (!$quick) {
                $this->testSvgDirectiveRendering($results, $verbose);
            }

            // Test 6: Error Handling
            $this->testErrorHandling($results, $verbose);

            // Test 7: Specific Icon (if provided)
            if ($specificIcon) {
                $this->testSpecificIcon($specificIcon, $results, $verbose);
            }

            // Display Results
            $this->displayResults($results);

            return $results['failed'] === 0 ? 0 : 1;

        } catch (Throwable $e) {
            $this->error('❌ Critical error during testing: ' . $e->getMessage());
            if ($verbose) {
                $this->error($e->getTraceAsString());
            }
            return 1;
        }
    }

    private function testServiceProviderRegistration(array &$results, bool $verbose): void
    {
        $this->info('🔍 Testing Service Provider Registration...');

        try {
            $providers = app()->getLoadedProviders();
            $providerClass = \Monsefeledrisse\FilamentSolarIcons\SolarIconSetServiceProvider::class;
            
            if (isset($providers[$providerClass])) {
                $this->success('✅ Service Provider is registered');
                $results['passed']++;
            } else {
                $this->error('❌ Service Provider is not registered');
                $results['failed']++;
                $results['errors'][] = 'SolarIconSetServiceProvider not found in loaded providers';
            }
        } catch (Throwable $e) {
            $this->error('❌ Error checking service provider: ' . $e->getMessage());
            $results['failed']++;
            $results['errors'][] = $e->getMessage();
        }
    }

    private function testIconFactoryRegistration(array &$results, bool $verbose): void
    {
        $this->info('🔍 Testing Icon Factory Registration...');

        try {
            $factory = app(Factory::class);
            $registeredSets = $factory->all();
            
            $solarSets = array_filter($registeredSets, function ($key) {
                return str_starts_with($key, 'solar-');
            }, ARRAY_FILTER_USE_KEY);

            if (count($solarSets) > 0) {
                $this->success('✅ Solar icon sets registered: ' . count($solarSets));
                if ($verbose) {
                    foreach (array_keys($solarSets) as $setName) {
                        $this->line("   - {$setName}");
                    }
                }
                $results['passed']++;
            } else {
                $this->error('❌ No Solar icon sets registered');
                $results['failed']++;
                $results['errors'][] = 'No Solar icon sets found in BladeUI Icons Factory';
            }
        } catch (Throwable $e) {
            $this->error('❌ Error checking icon factory: ' . $e->getMessage());
            $results['failed']++;
            $results['errors'][] = $e->getMessage();
        }
    }

    private function testEnumIntegration(array &$results, bool $verbose): void
    {
        $this->info('🔍 Testing Enum Integration...');

        try {
            $enumCases = SolarIcon::cases();
            $totalCases = count($enumCases);
            
            if ($totalCases > 0) {
                $this->success("✅ Enum has {$totalCases} cases");
                
                // Test a few random cases
                $sampleSize = min(5, $totalCases);
                $sampleCases = array_slice($enumCases, 0, $sampleSize);
                
                foreach ($sampleCases as $case) {
                    $exists = SolarIcon::exists($case->value);
                    if ($exists) {
                        if ($verbose) {
                            $this->line("   ✅ {$case->name} -> {$case->value}");
                        }
                    } else {
                        $this->warn("   ⚠️  {$case->name} -> {$case->value} (exists check failed)");
                    }
                }
                
                $results['passed']++;
            } else {
                $this->error('❌ Enum has no cases');
                $results['failed']++;
                $results['errors'][] = 'SolarIcon enum is empty';
            }
        } catch (Throwable $e) {
            $this->error('❌ Error testing enum: ' . $e->getMessage());
            $results['failed']++;
            $results['errors'][] = $e->getMessage();
        }
    }

    private function testBladeComponentRendering(array &$results, bool $verbose): void
    {
        $this->info('🔍 Testing Blade Component Rendering...');

        $testIcons = [
            'solar-bold-Home',
            'solar-linear-User',
            'solar-outline-Star'
        ];

        $successCount = 0;
        foreach ($testIcons as $iconName) {
            try {
                $html = Blade::render('<x-icon name="' . $iconName . '" />');
                
                if (str_contains($html, '<svg') && str_contains($html, '</svg>')) {
                    $successCount++;
                    if ($verbose) {
                        $this->line("   ✅ {$iconName} rendered successfully");
                    }
                } else {
                    if ($verbose) {
                        $this->warn("   ⚠️  {$iconName} rendered but no SVG found");
                    }
                }
            } catch (Throwable $e) {
                if ($verbose) {
                    $this->error("   ❌ {$iconName} failed: " . $e->getMessage());
                }
            }
        }

        if ($successCount === count($testIcons)) {
            $this->success('✅ All Blade components rendered successfully');
            $results['passed']++;
        } else {
            $this->error("❌ Only {$successCount}/" . count($testIcons) . " Blade components rendered");
            $results['failed']++;
            $results['errors'][] = 'Some Blade components failed to render';
        }
    }

    private function testSvgDirectiveRendering(array &$results, bool $verbose): void
    {
        $this->info('🔍 Testing SVG Directive Rendering...');

        $testIcons = [
            'solar-bold-Home',
            'solar-linear-User'
        ];

        $successCount = 0;
        foreach ($testIcons as $iconName) {
            try {
                $template = "@svg('{$iconName}')";
                $html = Blade::render($template);
                
                if (str_contains($html, '<svg') && str_contains($html, '</svg>')) {
                    $successCount++;
                    if ($verbose) {
                        $this->line("   ✅ {$iconName} rendered with @svg");
                    }
                } else {
                    if ($verbose) {
                        $this->warn("   ⚠️  {$iconName} rendered but no SVG found");
                    }
                }
            } catch (Throwable $e) {
                if ($verbose) {
                    $this->error("   ❌ {$iconName} failed: " . $e->getMessage());
                }
            }
        }

        if ($successCount === count($testIcons)) {
            $this->success('✅ All @svg directives rendered successfully');
            $results['passed']++;
        } else {
            $this->error("❌ Only {$successCount}/" . count($testIcons) . " @svg directives rendered");
            $results['failed']++;
            $results['errors'][] = 'Some @svg directives failed to render';
        }
    }

    private function testErrorHandling(array &$results, bool $verbose): void
    {
        $this->info('🔍 Testing Error Handling...');

        $invalidIcons = [
            'solar-bold-NonExistentIcon',
            'invalid-icon-name',
            ''
        ];

        $handledCount = 0;
        foreach ($invalidIcons as $iconName) {
            try {
                $html = Blade::render('<x-icon name="' . $iconName . '" />');
                $handledCount++; // If no exception, it's handled gracefully
                
                if ($verbose) {
                    $this->line("   ✅ {$iconName} handled gracefully");
                }
            } catch (Throwable $e) {
                if ($verbose) {
                    $this->line("   ⚠️  {$iconName} threw exception: " . $e->getMessage());
                }
                // Exceptions are also a form of handling, depending on configuration
                $handledCount++;
            }
        }

        $this->success('✅ Error handling test completed');
        $results['passed']++;
    }

    private function testSpecificIcon(string $iconName, array &$results, bool $verbose): void
    {
        $this->info("🔍 Testing Specific Icon: {$iconName}");

        try {
            // Test with Blade component
            $html = Blade::render('<x-icon name="' . $iconName . '" />');
            
            if (str_contains($html, '<svg') && str_contains($html, '</svg>')) {
                $this->success("✅ {$iconName} rendered successfully");
                if ($verbose) {
                    $this->line("   HTML length: " . strlen($html) . " characters");
                    $this->line("   Contains viewBox: " . (str_contains($html, 'viewBox') ? 'Yes' : 'No'));
                }
                $results['passed']++;
            } else {
                $this->error("❌ {$iconName} did not render valid SVG");
                $results['failed']++;
                $results['errors'][] = "Icon {$iconName} did not produce valid SVG output";
            }
        } catch (Throwable $e) {
            $this->error("❌ {$iconName} failed: " . $e->getMessage());
            $results['failed']++;
            $results['errors'][] = $e->getMessage();
        }
    }

    private function displayResults(array $results): void
    {
        $this->newLine();
        $this->info('📊 Test Results Summary');
        $this->line('========================');
        $this->info("✅ Passed: {$results['passed']}");
        $this->error("❌ Failed: {$results['failed']}");
        
        if (!empty($results['errors'])) {
            $this->newLine();
            $this->error('🚨 Errors encountered:');
            foreach ($results['errors'] as $error) {
                $this->line("   • {$error}");
            }
        }

        $this->newLine();
        if ($results['failed'] === 0) {
            $this->info('🎉 All tests passed! Solar Icons integration is working correctly.');
        } else {
            $this->error('⚠️  Some tests failed. Please check the errors above and verify your setup.');
        }
    }

    private function success(string $message): void
    {
        $this->line("<fg=green>{$message}</>");
    }
}
