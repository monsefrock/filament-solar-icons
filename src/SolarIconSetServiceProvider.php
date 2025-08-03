<?php

declare(strict_types=1);

namespace Monsefeledrisse\FilamentSolarIcons;

use BladeUI\Icons\Factory;
use Illuminate\Support\ServiceProvider;
use Monsefeledrisse\FilamentSolarIcons\Commands\SolarIconBrowserCommand;

/**
 * Solar Icons Service Provider
 *
 * Registers Solar icon sets with BladeUI Icons Factory following
 * the same pattern as BladeHeroicons for consistency and simplicity.
 *
 * @package Monsefeledrisse\FilamentSolarIcons
 */
class SolarIconSetServiceProvider extends ServiceProvider
{

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/solar-icons.php',
            'solar-icons'
        );
    }

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->registerIcons();
        $this->registerCommands();
        $this->publishAssets();
    }

    /**
     * Register Solar icon sets with BladeUI Icons Factory.
     */
    protected function registerIcons(): void
    {
        $factory = $this->app->make(Factory::class);

        $sets = config('solar-icons.sets', [
            'solar-bold',
            'solar-bold-duotone',
            'solar-broken',
            'solar-line-duotone',
            'solar-linear',
            'solar-outline',
        ]);

        // Register individual sets for backward compatibility
        foreach ($sets as $set) {
            $this->registerIconSet($factory, $set);
        }

        // Also register all icons in the default set for easy access
        $this->registerAllIconsInDefaultSet($factory, $sets);
    }

    /**
     * Register a single icon set with flattened structure.
     */
    protected function registerIconSet($factory, string $set): void
    {
        $sourcePath = __DIR__ . "/../resources/icons/solar/{$set}";

        if (!is_dir($sourcePath)) {
            return;
        }

        // Create a temporary flattened structure for BladeUI Icons
        $flattenedPath = $this->createFlattenedIconSet($sourcePath, $set);

        if ($flattenedPath && is_dir($flattenedPath)) {
            $factory->add($set, [
                'path' => $flattenedPath,
                'prefix' => $set,
                'class' => config('solar-icons.class', ''),
                'attributes' => config('solar-icons.attributes', []),
            ]);
        }
    }

    /**
     * Create a flattened icon set structure for BladeUI Icons.
     */
    protected function createFlattenedIconSet(string $sourcePath, string $set): ?string
    {
        $tempPath = sys_get_temp_dir() . "/solar-icons/{$set}";

        // Always (re)build the flattened icon set to ensure latest changes
        if (!is_dir($tempPath) && !mkdir($tempPath, 0755, true)) {
            return null;
        }

        // Clear existing files then regenerate
        $this->clearDirectory($tempPath);

        // Recursively find all SVG files and copy them with flattened names
        $this->flattenIconDirectory($sourcePath, $tempPath);

        return $tempPath;
    }

    /**
     * Recursively flatten icon directory structure.
     */
    protected function flattenIconDirectory(string $sourceDir, string $targetDir): void
    {
        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourceDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            $count = 0;
            foreach ($iterator as $file) {
                if ($file->isFile() && strtolower($file->getExtension()) === 'svg') {
                    $filename = $file->getFilename();
                    $targetFile = $targetDir . '/' . $filename;

                    // Copy the file to the flattened structure
                    if (copy($file->getPathname(), $targetFile)) {
                        $count++;
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently handle errors
        }
    }

    /**
     * Register all icons in the default set with their full names.
     */
    protected function registerAllIconsInDefaultSet($factory, array $sets): void
    {
        $tempPath = sys_get_temp_dir() . "/solar-icons/all";

        // Always (re)build the combined icon set
        if (!is_dir($tempPath) && !mkdir($tempPath, 0755, true)) {
            return;
        }

        // Clear existing files
        $this->clearDirectory($tempPath);

        // Copy all icons with their full names (set-iconname.svg)
        foreach ($sets as $set) {
            $sourcePath = __DIR__ . "/../resources/icons/solar/{$set}";

            if (is_dir($sourcePath)) {
                $this->flattenIconDirectoryWithPrefix($sourcePath, $tempPath, $set);
            }
        }

        // Register the combined set as the default
        $factory->add('default', [
            'path' => $tempPath,
            'prefix' => '',
            'class' => config('solar-icons.class', ''),
            'attributes' => config('solar-icons.attributes', []),
        ]);
    }

    /**
     * Register console commands.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SolarIconBrowserCommand::class,
            ]);
        }
    }



    /**
     * Recursively flatten icon directory structure with set prefix.
     */
    protected function flattenIconDirectoryWithPrefix(string $sourceDir, string $targetDir, string $setPrefix): void
    {
        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($sourceDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            $count = 0;
            foreach ($iterator as $file) {
                if ($file->isFile() && strtolower($file->getExtension()) === 'svg') {
                    $filename = $file->getBasename('.svg');
                    $prefixedFilename = $setPrefix . '-' . $filename . '.svg';
                    $targetFile = $targetDir . '/' . $prefixedFilename;

                    // Copy the file to the flattened structure with prefix
                    if (copy($file->getPathname(), $targetFile)) {
                        $count++;
                    }
                }
            }

            // Only log in debug mode
            if (config('app.debug', false) && function_exists('error_log')) {
                error_log("Solar Icons: Flattened {$count} files from {$sourceDir} to {$targetDir} with prefix {$setPrefix}");
            }
        } catch (\Exception $e) {
            if (function_exists('error_log')) {
                error_log("Solar Icons: Error flattening directory {$sourceDir}: " . $e->getMessage());
            }
        }
    }

    /**
     * Clear all files in a directory.
     */
    protected function clearDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $files = glob($directory . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Publish assets and configuration.
     */
    protected function publishAssets(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__ . '/../config/solar-icons.php' => config_path('solar-icons.php'),
        ], 'solar-icons-config');

        // Publish SVG assets
        $this->publishes([
            __DIR__ . '/../resources/icons' => public_path('vendor/solar-icons'),
        ], 'solar-icons');
    }
}
