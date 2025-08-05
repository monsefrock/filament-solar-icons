<?php

declare(strict_types=1);

namespace Monsefeledrisse\LaravelSolarIcons;

use BladeUI\Icons\Factory;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use Monsefeledrisse\LaravelSolarIcons\Commands\SolarIconBrowserCommand;
use Monsefeledrisse\LaravelSolarIcons\Commands\GenerateIdeHelperCommand;
use Monsefeledrisse\LaravelSolarIcons\Commands\PerformanceAnalysisCommand;

/**
 * Solar Icons Service Provider
 *
 * Registers Solar icon sets with BladeUI Icons Factory for Laravel applications.
 * Provides seamless integration with BladeUI Icons package.
 *
 * @package Monsefeledrisse\LaravelSolarIcons
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
     * @throws Exception
     */
    public function boot(): void
    {
        // Use lazy loading for better performance
        if (config('solar-icons.performance.lazy_loading', true)) {
            $this->registerIconsLazily();
        } else {
            $this->registerIcons();
        }

        $this->registerCommands();
        $this->publishAssets();
    }

    /**
     * Register Solar icon sets with BladeUI Icons Factory using lazy loading.
     * @throws BindingResolutionException
     */
    protected function registerIconsLazily(): void
    {
        try {
            $factory = $this->app->make(Factory::class);
        } catch (Exception $e) {
            // If BladeUI Icons Factory is not available, skip icon registration
            if (config('app.debug', false)) {
                throw $e;
            }
            return;
        }

        $sets = config('solar-icons.sets', [
            'solar-bold',
            'solar-bold-duotone',
            'solar-broken',
            'solar-line-duotone',
            'solar-linear',
            'solar-outline',
        ]);

        // Get preload sets for optimization
        $preloadSets = $this->getPreloadSets();

        // Register all sets, but optimize the ones that are preloaded
        foreach ($sets as $set) {
            try {
                if (in_array($set, $preloadSets)) {
                    // Preload commonly used sets with full processing
                    $this->registerIconSet($factory, $set);
                } else {
                    // Register other sets with minimal processing (still register them for compatibility)
                    $this->registerIconSetOptimized($factory, $set);
                }
            } catch (\Throwable $e) {
                // Silently skip problematic sets to maintain graceful error handling
                if (config('app.debug', false)) {
                    error_log("Solar Icons: Failed to register set {$set}: " . $e->getMessage());
                }
            }
        }

        // Register default set with caching
        try {
            $this->registerDefaultSetWithCaching($factory, $sets);
        } catch (\Throwable $e) {
            // Gracefully handle default set registration errors
            if (config('app.debug', false)) {
                error_log("Solar Icons: Failed to register default set: " . $e->getMessage());
            }
        }
    }

    /**
     * Register Solar icon sets with BladeUI Icons Factory.
     * @throws BindingResolutionException
     */
    protected function registerIcons(): void
    {
        try {
            $factory = $this->app->make(Factory::class);
        } catch (Exception $e) {
            // If BladeUI Icons Factory is not available, skip icon registration
            // This allows the package to be installed without breaking the application
            if (config('app.debug', false)) {
                throw $e;
            }
            return;
        }

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
            try {
                $this->registerIconSet($factory, $set);
            } catch (\Throwable $e) {
                // Silently skip problematic sets to maintain graceful error handling
                if (config('app.debug', false)) {
                    error_log("Solar Icons: Failed to register set {$set}: " . $e->getMessage());
                }
            }
        }

        // Also, register all icons in the default set for easy access
        try {
            $this->registerAllIconsInDefaultSet($factory, $sets);
        } catch (\Throwable $e) {
            // Gracefully handle default set registration errors
            if (config('app.debug', false)) {
                error_log("Solar Icons: Failed to register default set: " . $e->getMessage());
            }
        }
    }

    /**
     * Register a single icon set with a flattened structure.
     */
    protected function registerIconSet($factory, string $set): void
    {
        $sourcePath = __DIR__ . "/../resources/icons/solar/$set";

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
        $tempPath = sys_get_temp_dir() . "/solar-icons/$set";

        // Check if we should force rebuild or if directory doesn't exist
        $forceRebuild = config('solar-icons.development.force_rebuild', false);
        $shouldRebuild = $forceRebuild || !is_dir($tempPath) || $this->isDirectoryEmpty($tempPath);

        if (!$shouldRebuild) {
            return $tempPath;
        }

        // Create directory if it doesn't exist
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

        // Check if we should force rebuild or if directory doesn't exist
        $forceRebuild = config('solar-icons.development.force_rebuild', false);
        $shouldRebuild = $forceRebuild || !is_dir($tempPath) || $this->isDirectoryEmpty($tempPath);

        if (!$shouldRebuild && is_dir($tempPath)) {
            // Register the existing combined set as the default
            $factory->add('default', [
                'path' => $tempPath,
                'prefix' => '',
                'class' => config('solar-icons.class', ''),
                'attributes' => config('solar-icons.attributes', []),
            ]);
            return;
        }

        // Create directory if it doesn't exist
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
                GenerateIdeHelperCommand::class,
                PerformanceAnalysisCommand::class,
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
                    // Convert filename to lowercase for consistency
                    $lowercaseFilename = strtolower($filename);
                    $prefixedFilename = $setPrefix . '-' . $lowercaseFilename . '.svg';
                    $targetFile = $targetDir . '/' . $prefixedFilename;

                    // Copy the file to the flattened structure with prefix
                    if (copy($file->getPathname(), $targetFile)) {
                        $count++;
                    }
                }
            }

            // Only log if explicitly enabled in configuration
            if (config('solar-icons.development.log_flattening', false)) {
                $this->logFlatteningOperation($count, $sourceDir, $targetDir, $setPrefix);
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

    /**
     * Check if a directory is empty.
     */
    protected function isDirectoryEmpty(string $directory): bool
    {
        if (!is_dir($directory)) {
            return true;
        }

        $files = glob($directory . '/*');
        return empty($files);
    }

    /**
     * Log flattening operation using Laravel's logging system.
     */
    protected function logFlatteningOperation(int $count, string $sourceDir, string $targetDir, string $setPrefix): void
    {
        try {
            if (function_exists('logger')) {
                logger()->debug("Solar Icons: Flattened {$count} files from {$sourceDir} to {$targetDir} with prefix {$setPrefix}");
            } elseif (function_exists('error_log')) {
                error_log("Solar Icons: Flattened {$count} files from {$sourceDir} to {$targetDir} with prefix {$setPrefix}");
            }
        } catch (\Throwable $e) {
            // Silently ignore logging errors
        }
    }

    /**
     * Get the list of icon sets to preload for better performance.
     */
    protected function getPreloadSets(): array
    {
        $preloadConfig = config('solar-icons.performance.preload_sets', 'solar-outline,solar-linear');

        if (empty($preloadConfig)) {
            return [];
        }

        return array_map('trim', explode(',', $preloadConfig));
    }

    /**
     * Register an icon set with optimizations for non-preloaded sets.
     */
    protected function registerIconSetOptimized($factory, string $set): void
    {
        $sourcePath = __DIR__ . "/../resources/icons/solar/$set";

        if (!is_dir($sourcePath)) {
            return;
        }

        // For non-preloaded sets, create a minimal flattened structure
        $flattenedPath = $this->createOptimizedFlattenedIconSet($sourcePath, $set);

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
     * Register an icon set lazily (only when first accessed).
     */
    protected function registerIconSetLazily($factory, string $set): void
    {
        // For now, we'll register normally but skip the heavy processing
        // True lazy loading would require changes to BladeUI Icons Factory
        $this->registerIconSet($factory, $set);
    }

    /**
     * Register the default icon set with caching for better performance.
     */
    protected function registerDefaultSetWithCaching($factory, array $sets): void
    {
        $cacheEnabled = config('solar-icons.performance.cache_enabled', true);

        if ($cacheEnabled && function_exists('cache')) {
            $cacheKey = 'solar-icons.default-set-path';
            $cacheTtl = config('solar-icons.performance.cache_ttl', 3600);

            try {
                $tempPath = cache()->remember($cacheKey, $cacheTtl, function() use ($sets) {
                    return $this->createDefaultIconSet($sets);
                });
            } catch (\Throwable $e) {
                // Fall back to non-cached version
                $tempPath = $this->createDefaultIconSet($sets);
            }
        } else {
            $tempPath = $this->createDefaultIconSet($sets);
        }

        if ($tempPath && is_dir($tempPath)) {
            $factory->add('default', [
                'path' => $tempPath,
                'prefix' => '',
                'class' => config('solar-icons.class', ''),
                'attributes' => config('solar-icons.attributes', []),
            ]);
        }
    }

    /**
     * Get or create flattened icon set with caching.
     */
    protected function getOrCreateFlattenedIconSet(string $set): ?string
    {
        $sourcePath = __DIR__ . "/../resources/icons/solar/$set";

        if (!is_dir($sourcePath)) {
            return null;
        }

        return $this->createFlattenedIconSet($sourcePath, $set);
    }

    /**
     * Create an optimized flattened icon set with minimal processing.
     */
    protected function createOptimizedFlattenedIconSet(string $sourcePath, string $set): ?string
    {
        $tempPath = sys_get_temp_dir() . "/solar-icons/$set";

        // Check if we should force rebuild or if directory doesn't exist
        $forceRebuild = config('solar-icons.development.force_rebuild', false);
        $shouldRebuild = $forceRebuild || !is_dir($tempPath) || $this->isDirectoryEmpty($tempPath);

        if (!$shouldRebuild) {
            return $tempPath;
        }

        // Create directory if it doesn't exist
        if (!is_dir($tempPath) && !mkdir($tempPath, 0755, true)) {
            return null;
        }

        // Clear existing files then regenerate with minimal processing
        $this->clearDirectory($tempPath);

        // Use a more efficient approach for non-preloaded sets
        $this->flattenIconDirectoryOptimized($sourcePath, $tempPath);

        return $tempPath;
    }

    /**
     * Create the default icon set with all icons.
     */
    protected function createDefaultIconSet(array $sets): ?string
    {
        $tempPath = sys_get_temp_dir() . "/solar-icons/all";

        // Check if we should force rebuild or if directory doesn't exist
        $forceRebuild = config('solar-icons.development.force_rebuild', false);
        $shouldRebuild = $forceRebuild || !is_dir($tempPath) || $this->isDirectoryEmpty($tempPath);

        if (!$shouldRebuild && is_dir($tempPath)) {
            return $tempPath;
        }

        // Create directory if it doesn't exist
        if (!is_dir($tempPath) && !mkdir($tempPath, 0755, true)) {
            return null;
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

        return $tempPath;
    }

    /**
     * Optimized flattening for non-preloaded icon sets.
     */
    protected function flattenIconDirectoryOptimized(string $sourceDir, string $targetDir): void
    {
        try {
            // Use a simpler approach that doesn't process every file immediately
            $files = glob($sourceDir . '/*.svg');

            foreach ($files as $file) {
                $filename = basename($file);
                $targetFile = $targetDir . '/' . $filename;

                // Simple copy without extensive processing
                copy($file, $targetFile);
            }
        } catch (\Exception $e) {
            // Silently handle errors for non-critical sets
        }
    }
}
