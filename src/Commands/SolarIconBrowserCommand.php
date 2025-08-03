<?php

namespace Monsefeledrisse\FilamentSolarIcons\Commands;

use Illuminate\Console\Command;
use Monsefeledrisse\FilamentSolarIcons\SolarIconHelper;
use Monsefeledrisse\FilamentSolarIcons\SolarIcon;

class SolarIconBrowserCommand extends Command
{
    protected $signature = 'solar-icons:browse 
                           {search? : Search term to filter icons}
                           {--style= : Filter by icon style (bold, outline, linear, etc.)}
                           {--limit=20 : Limit number of results}
                           {--enum : Show enum cases for Filament v4}';

    protected $description = 'Browse and search Solar icons';

    public function handle()
    {
        $search = $this->argument('search');
        $style = $this->option('style');
        $limit = (int) $this->option('limit');
        $showEnum = $this->option('enum');

        $this->info('🌟 Solar Icons Browser');
        $this->line('');

        if ($search) {
            $this->searchIcons($search, $limit, $showEnum);
        } elseif ($style) {
            $this->browseByStyle($style, $limit, $showEnum);
        } else {
            $this->showOverview();
        }
    }

    protected function searchIcons(string $search, int $limit, bool $showEnum): void
    {
        $this->info("🔍 Searching for icons matching: '{$search}'");
        $this->line('');

        $icons = SolarIconHelper::searchIcons($search)->take($limit);

        if ($icons->isEmpty()) {
            $this->warn("No icons found matching '{$search}'");
            $this->suggestAlternatives($search);
            return;
        }

        $this->displayIcons($icons, $showEnum);
    }

    protected function browseByStyle(string $style, int $limit, bool $showEnum): void
    {
        $this->info("🎨 Browsing {$style} style icons");
        $this->line('');

        $icons = SolarIconHelper::getIconsByStyle($style)->take($limit);

        if ($icons->isEmpty()) {
            $this->warn("No icons found for style '{$style}'");
            $this->showAvailableStyles();
            return;
        }

        $this->displayIcons($icons, $showEnum);
    }

    protected function showOverview(): void
    {
        $this->info('📊 Solar Icons Overview');
        $this->line('');

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
        $this->info('Usage Examples:');
        $this->line('  # Search for icons');
        $this->line('  php artisan solar-icons:browse user');
        $this->line('');
        $this->line('  # Browse by style');
        $this->line('  php artisan solar-icons:browse --style=linear');
        $this->line('');
        $this->line('  # Show enum cases for Filament v4');
        $this->line('  php artisan solar-icons:browse home --enum');
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
