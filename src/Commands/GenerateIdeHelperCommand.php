<?php

declare(strict_types=1);

namespace Monsefeledrisse\LaravelSolarIcons\Commands;

use Illuminate\Console\Command;
use Monsefeledrisse\LaravelSolarIcons\SolarIconHelper;

/**
 * Generate IDE Helper Command
 *
 * Generates IDE helper files for better auto-completion support of Solar icon Blade components.
 * This command creates static component stubs that IDEs can analyze for auto-completion.
 *
 * @package Monsefeledrisse\LaravelSolarIcons\Commands
 */
class GenerateIdeHelperCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'solar-icons:ide-helper
                           {--output= : Output file path (default: _ide_helper_blade_components.php)}
                           {--force : Overwrite existing file}';

    /**
     * The console command description.
     */
    protected $description = 'Generate IDE helper file for Solar icon Blade components auto-completion';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $outputPath = $this->option('output') ?: base_path('_ide_helper_blade_components.php');
            $force = $this->option('force');

            if (file_exists($outputPath) && !$force) {
                $this->error("File already exists: {$outputPath}");
                $this->info("Use --force to overwrite or specify a different --output path");
                return self::FAILURE;
            }

            $this->info('🔍 Scanning Solar icons...');
            $icons = SolarIconHelper::getAllIconFiles();

            if ($icons->isEmpty()) {
                $this->error('No Solar icons found. Please check your icon directories.');
                return self::FAILURE;
            }

            $this->info("📝 Generating IDE helper for {$icons->count()} icons...");
            $content = $this->generateIdeHelperContent($icons);

            if (file_put_contents($outputPath, $content) === false) {
                $this->error("Failed to write IDE helper file: {$outputPath}");
                return self::FAILURE;
            }

            $this->info("✅ IDE helper generated successfully: {$outputPath}");
            $this->info('💡 Add this file to your IDE for better auto-completion support');
            $this->info('⚠️  Remember to add this file to your .gitignore if you don\'t want to commit it');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("An error occurred: {$e->getMessage()}");
            if ($this->getOutput()->isVerbose()) {
                $this->line($e->getTraceAsString());
            }
            return self::FAILURE;
        }
    }

    /**
     * Generate the IDE helper file content.
     */
    protected function generateIdeHelperContent($icons): string
    {
        $components = [];
        $iconsBySet = $icons->groupBy('style');

        foreach ($iconsBySet as $style => $styleIcons) {
            foreach ($styleIcons as $icon) {
                $componentName = $icon['key'];
                $iconName = $icon['name'];
                
                // Generate component stub
                $components[] = $this->generateComponentStub($componentName, $iconName, $style);
            }
        }

        $componentsContent = implode("\n\n", $components);
        $timestamp = date('Y-m-d H:i:s');
        $totalComponents = count($components);

        return <<<PHP
<?php

/**
 * IDE Helper for Solar Icon Blade Components
 * 
 * This file provides IDE auto-completion support for Solar icon Blade components.
 * It contains static component stubs that IDEs can analyze for better developer experience.
 * 
 * Generated automatically on {$timestamp}
 * Total components: {$totalComponents}
 * 
 * Usage examples:
 * <x-solar-linear-home class="w-6 h-6" />
 * <x-solar-bold-user class="w-8 h-8 text-blue-500" />
 * <x-solar-outline-settings />
 * 
 * @package Monsefeledrisse\LaravelSolarIcons
 */

namespace Illuminate\View\AnonymousComponent {
    // This namespace declaration helps IDEs understand these are Blade components
}

{$componentsContent}

PHP;
    }

    /**
     * Generate a component stub for IDE auto-completion.
     */
    protected function generateComponentStub(string $componentName, string $iconName, string $style): string
    {
        $className = $this->generateComponentClassName($componentName);
        
        return <<<PHP
/**
 * Solar Icon Component: {$iconName}
 * Style: {$style}
 * Usage: <x-{$componentName} class="w-6 h-6" />
 */
class {$className}
{
    public function __construct(
        public ?string \$class = null,
        public ?string \$style = null,
        public ?int \$width = null,
        public ?int \$height = null,
        public ?string \$fill = null,
        public ?string \$stroke = null,
        public ?string \$color = null,
        public array \$attributes = []
    ) {}
}PHP;
    }

    /**
     * Generate a valid PHP class name from component name.
     */
    protected function generateComponentClassName(string $componentName): string
    {
        // Convert component name to PascalCase class name
        $className = str_replace(['-', '_'], ' ', $componentName);
        $className = ucwords($className);
        $className = str_replace(' ', '', $className);
        
        // Ensure it starts with a letter
        if (is_numeric(substr($className, 0, 1))) {
            $className = 'Component' . $className;
        }
        
        return $className . 'Component';
    }
}
