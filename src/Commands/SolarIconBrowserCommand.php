<?php

declare(strict_types=1);

namespace Monsefeledrisse\LaravelSolarIcons\Commands;

use Illuminate\Console\Command;
use Monsefeledrisse\LaravelSolarIcons\SolarIconHelper;
use Monsefeledrisse\LaravelSolarIcons\SolarIcon;

/**
 * Solar Icon Browser Command
 *
 * Provides a command-line interface for browsing and searching Solar icons.
 * Supports filtering by style, searching by name, and displaying usage examples
 * for Laravel applications.
 *
 * @package Monsefeledrisse\LaravelSolarIcons\Commands
 */
class SolarIconBrowserCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'solar-icons:browse
                           {search? : Search term to filter icons}
                           {--style= : Filter by icon style (bold, outline, linear, etc.)}
                           {--limit=20 : Limit number of results}
                           {--enum : Show enum cases for Laravel usage}
                           {--clear-cache : Clear the icon cache before browsing}';

    /**
     * The console command description.
     */
    protected $description = 'Browse and search Solar icons for Laravel applications';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            // Clear cache if requested
            if ($this->option('clear-cache')) {
                $this->clearCache();
            }

            $search = $this->argument('search');
            $style = $this->option('style');
            $limit = max(1, (int) $this->option('limit')); // Ensure positive limit
            $showEnum = $this->option('enum');

            $this->displayHeader();

            if ($search) {
                return $this->searchIcons($search, $limit, $showEnum);
            } elseif ($style) {
                return $this->browseByStyle($style, $limit, $showEnum);
            } else {
                return $this->showOverview();
            }
        } catch (\Throwable $e) {
            $this->error("An error occurred: {$e->getMessage()}");
            if ($this->getOutput()->isVerbose()) {
                $this->line($e->getTraceAsString());
            }
            return self::FAILURE;
        }
    }

    /**
     * Display the command header.
     */
    private function displayHeader(): void
    {
        $this->info('🌟 Solar Icons Browser');
        $this->line('');
    }

    /**
     * Clear the icon cache.
     */
    private function clearCache(): void
    {
        if (SolarIconHelper::clearCache()) {
            $this->info('✅ Icon cache cleared successfully');
        } else {
            $this->warn('⚠️  Failed to clear icon cache');
        }
        $this->line('');
    }

    /**
     * Search for icons matching the given query.
     */
    protected function searchIcons(string $search, int $limit, bool $showEnum): int
    {
        $this->info("🔍 Searching for icons matching: '{$search}'");
        $this->line('');

        $icons = SolarIconHelper::searchIcons($search)->take($limit);

        if ($icons->isEmpty()) {
            $this->warn("No icons found matching '{$search}'");
            $this->suggestAlternatives($search);
            return self::SUCCESS;
        }

        $this->displayIcons($icons, $showEnum);
        return self::SUCCESS;
    }

    /**
     * Browse icons by style.
     */
    protected function browseByStyle(string $style, int $limit, bool $showEnum): int
    {
        // Validate style
        $availableStyles = SolarIconHelper::getAvailableStyles();
        $normalizedStyle = str_starts_with($style, 'solar-') ? $style : "solar-{$style}";

        if (!in_array($normalizedStyle, $availableStyles, true)) {
            $this->error("❌ Unknown style: '{$style}'");
            $this->showAvailableStyles();
            return self::FAILURE;
        }

        $this->info("🎨 Browsing {$style} style icons");
        $this->line('');

        $icons = SolarIconHelper::getIconsByStyle($style)->take($limit);

        if ($icons->isEmpty()) {
            $this->warn("No icons found for style '{$style}'");
            $this->info("This might indicate missing icon files or a configuration issue.");
            return self::SUCCESS;
        }

        $this->displayIcons($icons, $showEnum);
        return self::SUCCESS;
    }

    /**
     * Show overview of available icons and usage examples.
     */
    protected function showOverview(): int
    {
        $this->info('📊 Solar Icons Overview');
        $this->line('');

        try {
            // Show statistics
            $totalIcons = SolarIconHelper::getAllIconFiles()->count();
            $this->info("Total Icons: {$totalIcons}");
            $this->line('');

            // Show available styles
            $this->info('Available Styles:');
            foreach (SolarIcon::getAvailableStyles() as $style => $description) {
                $count = SolarIconHelper::getIconsByStyle($style)->count();
                $this->line("  • {$style}: {$count} icons - {$description}");
            }
            $this->line('');

            // Show popular icons
            $this->info('Popular Icons:');
            $popular = SolarIconHelper::getPopularIcons();
            foreach (array_slice($popular, 0, 10, true) as $icon => $description) {
                $this->line("  • {$icon}: {$description}");
            }
            $this->line('');

            // Show usage examples
            $this->showUsageExamples();

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("Failed to load icon overview: {$e->getMessage()}");
            return self::FAILURE;
        }
    }

    /**
     * Show usage examples.
     */
    private function showUsageExamples(): void
    {
        $this->info('Usage Examples:');
        $this->line('  # Search for icons');
        $this->line('  php artisan solar-icons:browse user');
        $this->line('');
        $this->line('  # Browse by style');
        $this->line('  php artisan solar-icons:browse --style=linear');
        $this->line('');
        $this->line('  # Show enum cases for Filament v4');
        $this->line('  php artisan solar-icons:browse home --enum');
        $this->line('');
        $this->line('  # Clear cache and browse');
        $this->line('  php artisan solar-icons:browse --clear-cache');
        $this->line('');
        $this->line('  # Limit results');
        $this->line('  php artisan solar-icons:browse user --limit=5');
    }

    protected function displayIcons($icons, bool $showEnum): void
    {
        $headers = $showEnum ? ['Icon Name', 'Style', 'Enum Case (v4)', 'String (v3)'] : ['Icon Name', 'Style', 'Usage'];

        $rows = $icons->map(function ($icon) use ($showEnum) {
            $enumCase = $this->findEnumCase($icon['key']);
            
            if ($showEnum) {
                return [
                    $icon['name'],
                    str_replace('solar-', '', $icon['style']),
                    $enumCase ? "SolarIcon::{$enumCase}" : 'Not available',
                    $icon['key'],
                ];
            }

            return [
                $icon['name'],
                str_replace('solar-', '', $icon['style']),
                $icon['key'],
            ];
        })->toArray();

        $this->table($headers, $rows);
        
        $this->line('');
        $this->info("Showing {$icons->count()} icons");
        
        if ($showEnum) {
            $this->line('');
            $this->info('💡 Filament v4 Usage:');
            $this->line('  Action::make("example")->icon(SolarIcon::Home)');
            $this->line('');
            $this->info('💡 Filament v3 Usage:');
            $this->line('  Action::make("example")->icon("solar-linear-home")');
        }
    }

    protected function findEnumCase(string $iconKey): ?string
    {
        foreach (SolarIcon::cases() as $case) {
            if ($case->value === $iconKey) {
                return $case->name;
            }
        }
        return null;
    }

    protected function suggestAlternatives(string $search): void
    {
        $this->line('');
        $this->info('💡 Suggestions:');
        
        // Suggest similar terms
        $suggestions = [
            'user' => ['profile', 'account', 'person'],
            'home' => ['house', 'dashboard', 'main'],
            'settings' => ['config', 'gear', 'preferences'],
            'edit' => ['pen', 'modify', 'update'],
            'delete' => ['trash', 'remove', 'bin'],
            'search' => ['find', 'magnifier', 'lookup'],
        ];

        foreach ($suggestions as $term => $alternatives) {
            if (str_contains(strtolower($search), $term)) {
                $this->line("  Try searching for: " . implode(', ', $alternatives));
                break;
            }
        }

        $this->line('  Or browse by style: --style=linear');
        $this->line('  Or view all icons: php artisan solar-icons:browse');
    }

    protected function showAvailableStyles(): void
    {
        $this->line('');
        $this->info('Available styles:');
        foreach (SolarIcon::getAvailableStyles() as $style => $description) {
            $this->line("  • {$style}");
        }
    }
}
