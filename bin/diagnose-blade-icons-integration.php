#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Solar Icons Blade Integration Diagnostic Script
 *
 * This script diagnoses issues with Solar Icons integration with blade-ui-kit/blade-icons
 * and provides solutions for proper icon registration and usage.
 *
 * Usage: php bin/diagnose-blade-icons-integration.php
 *
 * @package Monsefeledrisse\FilamentSolarIcons
 */

// Ensure we're running from the project root
$projectRoot = dirname(__DIR__);
chdir($projectRoot);

// Autoload dependencies
if (!file_exists($projectRoot . '/vendor/autoload.php')) {
    echo "Error: Composer dependencies not installed. Run 'composer install' first.\n";
    exit(1);
}

require_once $projectRoot . '/vendor/autoload.php';

use Monsefeledrisse\FilamentSolarIcons\SolarIconHelper;

class BladeIconsDiagnostic
{
    private string $projectRoot;
    
    public function __construct(string $projectRoot)
    {
        $this->projectRoot = $projectRoot;
    }
    
    public function run(): void
    {
        $this->printHeader();
        
        echo "🔍 Diagnosing Solar Icons integration with blade-ui-kit/blade-icons...\n\n";
        
        // Step 1: Check icon file structure
        $this->checkIconFileStructure();
        
        // Step 2: Check service provider registration
        $this->checkServiceProviderRegistration();
        
        // Step 3: Check icon naming conventions
        $this->checkIconNamingConventions();
        
        // Step 4: Provide solutions
        $this->provideSolutions();
        
        $this->printFooter();
    }
    
    private function printHeader(): void
    {
        echo str_repeat("=", 70) . "\n";
        echo "SOLAR ICONS BLADE-UI-KIT INTEGRATION DIAGNOSTIC\n";
        echo str_repeat("=", 70) . "\n\n";
    }
    
    private function printFooter(): void
    {
        echo "\n" . str_repeat("=", 70) . "\n";
        echo "DIAGNOSTIC COMPLETE\n";
        echo str_repeat("=", 70) . "\n";
    }
    
    private function checkIconFileStructure(): void
    {
        echo "📁 CHECKING ICON FILE STRUCTURE\n";
        echo str_repeat("-", 40) . "\n";
        
        $iconBasePath = $this->projectRoot . '/resources/icons/solar';
        
        if (!is_dir($iconBasePath)) {
            echo "❌ Solar icons directory not found: {$iconBasePath}\n";
            return;
        }
        
        echo "✅ Solar icons directory exists: {$iconBasePath}\n";
        
        // Check each style directory
        $styles = ['solar-bold', 'solar-outline', 'solar-linear', 'solar-broken', 'solar-bold-duotone', 'solar-line-duotone'];
        
        foreach ($styles as $style) {
            $stylePath = $iconBasePath . '/' . $style;
            if (is_dir($stylePath)) {
                $iconCount = $this->countSvgFiles($stylePath);
                echo "✅ {$style}: {$iconCount} icons\n";
                
                // Show sample files
                $sampleFiles = $this->getSampleFiles($stylePath, 3);
                foreach ($sampleFiles as $file) {
                    echo "   📄 {$file}\n";
                }
            } else {
                echo "❌ {$style}: Directory not found\n";
            }
        }
        
        echo "\n";
    }
    
    private function checkServiceProviderRegistration(): void
    {
        echo "🔧 CHECKING SERVICE PROVIDER REGISTRATION\n";
        echo str_repeat("-", 40) . "\n";
        
        $serviceProviderClass = 'Monsefeledrisse\\FilamentSolarIcons\\SolarIconSetServiceProvider';
        
        if (class_exists($serviceProviderClass)) {
            echo "✅ Service Provider class exists: {$serviceProviderClass}\n";
        } else {
            echo "❌ Service Provider class not found: {$serviceProviderClass}\n";
        }
        
        // Check composer.json for auto-discovery
        $composerPath = $this->projectRoot . '/composer.json';
        if (file_exists($composerPath)) {
            $composer = json_decode(file_get_contents($composerPath), true);
            if (isset($composer['extra']['laravel']['providers'])) {
                $providers = $composer['extra']['laravel']['providers'];
                if (in_array($serviceProviderClass, $providers)) {
                    echo "✅ Service Provider registered in composer.json for auto-discovery\n";
                } else {
                    echo "❌ Service Provider not found in composer.json auto-discovery\n";
                }
            } else {
                echo "⚠️  No Laravel auto-discovery configuration found in composer.json\n";
            }
        }
        
        echo "\n";
    }
    
    private function checkIconNamingConventions(): void
    {
        echo "🏷️  CHECKING ICON NAMING CONVENTIONS\n";
        echo str_repeat("-", 40) . "\n";
        
        try {
            $icons = SolarIconHelper::getAllIconFiles();
            $sampleIcons = $icons->take(5);
            
            echo "Sample icon naming analysis:\n";
            foreach ($sampleIcons as $icon) {
                echo "📋 Icon Analysis:\n";
                echo "   Key: {$icon['key']}\n";
                echo "   Name: {$icon['name']}\n";
                echo "   Style: {$icon['style']}\n";
                
                // Check if this matches blade-ui-kit expectations
                $expectedBladeIconName = $this->getExpectedBladeIconName($icon);
                echo "   Expected Blade Icon Name: {$expectedBladeIconName}\n";
                
                // Check if file exists
                $filePath = $this->findIconFile($icon['name'], $icon['style']);
                if ($filePath) {
                    echo "   ✅ File exists: " . basename($filePath) . "\n";
                } else {
                    echo "   ❌ File not found\n";
                }
                echo "\n";
            }
            
        } catch (Exception $e) {
            echo "❌ Error analyzing icons: " . $e->getMessage() . "\n";
        }
        
        echo "\n";
    }
    
    private function provideSolutions(): void
    {
        echo "💡 SOLUTIONS AND RECOMMENDATIONS\n";
        echo str_repeat("-", 40) . "\n";
        
        echo "ISSUE: Solar icons not rendering with blade-ui-kit/blade-icons\n\n";
        
        echo "ROOT CAUSE:\n";
        echo "The blade-ui-kit/blade-icons package expects icon names to match the actual\n";
        echo "SVG file names, but the Solar Icons enum uses transformed names.\n\n";
        
        echo "SOLUTIONS:\n\n";
        
        echo "1. 📝 USE CORRECT ICON NAMES IN BLADE TEMPLATES\n";
        echo "   Instead of: <x-icon name=\"solar-bold-Home\" />\n";
        echo "   Use:        <x-icon name=\"solar-bold-home\" />\n";
        echo "   Or:         <x-icon name=\"solar-bold-facemask_circle\" />\n\n";
        
        echo "2. 🔧 UPDATE ENUM TO USE ACTUAL FILE NAMES\n";
        echo "   The enum should use the actual SVG file names:\n";
        echo "   case FacemaskCircle = 'solar-bold-facemask_circle';\n";
        echo "   Instead of: 'solar-bold-FacemaskCircle'\n\n";
        
        echo "3. 🛠️  REGISTER ICONS WITH PROPER PATHS\n";
        echo "   Ensure the service provider registers all subdirectories:\n";
        echo "   - resources/icons/solar/solar-bold/**/*.svg\n";
        echo "   - resources/icons/solar/solar-outline/**/*.svg\n";
        echo "   - etc.\n\n";
        
        echo "4. 🧪 TEST WITH ACTUAL FILE NAMES\n";
        echo "   Test examples:\n";
        echo "   @svg('solar-bold-facemask_circle')\n";
        echo "   <x-icon name=\"solar-linear-home\" />\n";
        echo "   <x-icon name=\"solar-outline-user\" />\n\n";
        
        echo "5. 📋 RECOMMENDED NEXT STEPS\n";
        echo "   a) Run: php bin/sync-solar-icons.php --dry-run\n";
        echo "   b) Update enum to use actual file names\n";
        echo "   c) Test with corrected icon names\n";
        echo "   d) Update documentation with correct usage examples\n\n";
        
        echo "6. 🔍 DEBUGGING COMMANDS\n";
        echo "   # Check registered icon sets\n";
        echo "   php artisan tinker\n";
        echo "   >>> app(\\BladeUI\\Icons\\Factory::class)->all()\n\n";
        echo "   # Test specific icon rendering\n";
        echo "   >>> Blade::render('<x-icon name=\"solar-bold-facemask_circle\" />')\n\n";
    }
    
    private function countSvgFiles(string $directory): int
    {
        $count = 0;
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'svg') {
                $count++;
            }
        }
        
        return $count;
    }
    
    private function getSampleFiles(string $directory, int $limit = 3): array
    {
        $files = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        $count = 0;
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'svg' && $count < $limit) {
                $relativePath = str_replace($directory . '/', '', $file->getPathname());
                $files[] = $relativePath;
                $count++;
            }
        }
        
        return $files;
    }
    
    private function getExpectedBladeIconName(array $icon): string
    {
        // For blade-ui-kit, the icon name should be: {set-name}-{file-name-without-extension}
        $style = $icon['style']; // e.g., 'solar-bold'
        $fileName = $icon['name']; // e.g., 'facemask_circle'
        
        return $style . '-' . $fileName;
    }
    
    private function findIconFile(string $iconName, string $style): ?string
    {
        $basePath = $this->projectRoot . '/resources/icons/solar/' . $style;
        
        if (!is_dir($basePath)) {
            return null;
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'svg') {
                $fileName = $file->getBasename('.svg');
                if ($fileName === $iconName) {
                    return $file->getPathname();
                }
            }
        }
        
        return null;
    }
}

// Run the diagnostic
$diagnostic = new BladeIconsDiagnostic($projectRoot);
$diagnostic->run();
