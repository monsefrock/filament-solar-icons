<?php

declare(strict_types=1);

namespace Monsefeledrisse\FilamentSolarIcons;

use BladeUI\Icons\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Monsefeledrisse\FilamentSolarIcons\Commands\SolarIconBrowserCommand;

/**
 * Solar Icon Set Service Provider
 *
 * Registers Solar icon sets with BladeUI Icons Factory and provides
 * configuration management for the Solar Icons package.
 *
 * @package Monsefeledrisse\FilamentSolarIcons
 */
class SolarIconSetServiceProvider extends ServiceProvider
{
    /**
     * Default icon sets configuration
     */
    private const DEFAULT_ICON_SETS = [
        'solar-bold' => [
            'enabled' => true,
            'path' => 'resources/icons/solar/solar-bold',
        ],
        'solar-bold-duotone' => [
            'enabled' => true,
            'path' => 'resources/icons/solar/solar-bold-duotone',
        ],
        'solar-broken' => [
            'enabled' => true,
            'path' => 'resources/icons/solar/solar-broken',
        ],
        'solar-line-duotone' => [
            'enabled' => true,
            'path' => 'resources/icons/solar/solar-line-duotone',
        ],
        'solar-linear' => [
            'enabled' => true,
            'path' => 'resources/icons/solar/solar-linear',
        ],
        'solar-outline' => [
            'enabled' => true,
            'path' => 'resources/icons/solar/solar-outline',
        ],
    ];

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        try {
            $this->registerIcons();
            $this->registerCommands();
            $this->publishConfiguration();
        } catch (\Throwable $e) {
            $this->handleBootError($e);
        }
    }

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
     * Register Solar icon sets with BladeUI Icons Factory.
     *
     * @throws \Exception When Factory cannot be resolved or icon registration fails
     */
    protected function registerIcons(): void
    {
        $iconSets = $this->getIconSetsConfiguration();
        $factory = $this->resolveIconFactory();

        $registeredCount = 0;
        $skippedCount = 0;

        foreach ($iconSets as $prefix => $config) {
            if (!$this->isIconSetEnabled($config)) {
                $skippedCount++;
                continue;
            }

            $path = $this->resolveIconSetPath($prefix, $config);

            if ($this->validateIconSetPath($path, $prefix)) {
                $this->registerIconSet($factory, $prefix, $path);
                $registeredCount++;
            } else {
                $skippedCount++;
            }
        }

        $this->logRegistrationSummary($registeredCount, $skippedCount);
    }

    /**
     * Get icon sets configuration from config or use defaults.
     */
    private function getIconSetsConfiguration(): array
    {
        $configSets = config('solar-icons.icon_sets', []);

        // Merge with defaults, ensuring all required keys exist
        $iconSets = [];
        foreach (self::DEFAULT_ICON_SETS as $prefix => $defaultConfig) {
            $iconSets[$prefix] = array_merge(
                $defaultConfig,
                $configSets[$prefix] ?? []
            );
        }

        return $iconSets;
    }

    /**
     * Resolve the BladeUI Icons Factory instance.
     *
     * @throws \Exception When Factory cannot be resolved
     */
    protected function resolveIconFactory(): Factory
    {
        try {
            return $this->app->make(Factory::class);
        } catch (\Throwable $e) {
            throw new \Exception(
                'Failed to resolve BladeUI Icons Factory. Make sure blade-ui-kit/blade-icons is installed.',
                0,
                $e
            );
        }
    }

    /**
     * Check if an icon set is enabled.
     */
    private function isIconSetEnabled(array $config): bool
    {
        return ($config['enabled'] ?? true) === true;
    }

    /**
     * Resolve the full path for an icon set.
     */
    private function resolveIconSetPath(string $prefix, array $config): string
    {
        if (isset($config['path']) && !empty($config['path'])) {
            // If path is relative, make it absolute from package root
            if (!str_starts_with($config['path'], '/')) {
                return __DIR__ . '/../' . $config['path'];
            }
            return $config['path'];
        }

        // Fallback to default path structure
        return __DIR__ . "/../resources/icons/solar/{$prefix}";
    }

    /**
     * Validate that an icon set path exists and is readable.
     */
    private function validateIconSetPath(string $path, string $prefix): bool
    {
        if (!is_dir($path)) {
            $this->logIconSetWarning($prefix, "Directory does not exist: {$path}");
            return false;
        }

        if (!is_readable($path)) {
            $this->logIconSetWarning($prefix, "Directory is not readable: {$path}");
            return false;
        }

        return true;
    }

    /**
     * Register a single icon set with the factory.
     */
    private function registerIconSet(Factory $factory, string $prefix, string $path): void
    {
        try {
            $factory->add($prefix, [
                'path' => $path,
                'prefix' => $prefix,
            ]);
        } catch (\Throwable $e) {
            $this->logIconSetError($prefix, "Failed to register icon set: {$e->getMessage()}");
        }
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
     * Publish configuration files.
     */
    protected function publishConfiguration(): void
    {
        $this->publishes([
            __DIR__ . '/../config/solar-icons.php' => config_path('solar-icons.php'),
        ], 'solar-icons-config');
    }

    /**
     * Handle boot errors gracefully.
     */
    private function handleBootError(\Throwable $e): void
    {
        $message = "Solar Icons Service Provider boot failed: {$e->getMessage()}";

        if (config('app.debug', false)) {
            throw new \Exception($message, 0, $e);
        }

        Log::error($message, [
            'exception' => $e,
            'trace' => $e->getTraceAsString(),
        ]);
    }

    /**
     * Log icon set registration warning.
     */
    private function logIconSetWarning(string $prefix, string $message): void
    {
        if (config('solar-icons.development.log_missing_icons', false)) {
            Log::warning("Solar Icons [{$prefix}]: {$message}");
        }
    }

    /**
     * Log icon set registration error.
     */
    private function logIconSetError(string $prefix, string $message): void
    {
        Log::error("Solar Icons [{$prefix}]: {$message}");
    }

    /**
     * Log registration summary.
     */
    private function logRegistrationSummary(int $registered, int $skipped): void
    {
        if (config('app.debug', false)) {
            Log::info("Solar Icons: Registered {$registered} icon sets, skipped {$skipped}");
        }
    }
}
