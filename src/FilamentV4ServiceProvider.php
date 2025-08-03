<?php

namespace Monsefeledrisse\FilamentSolarIcons;

use Filament\Support\Facades\FilamentIcon;
use Filament\View\PanelsIconAlias;
use Filament\Actions\View\ActionsIconAlias;
use Filament\Forms\View\FormsIconAlias;
use Filament\Tables\View\TablesIconAlias;
use Filament\Notifications\View\NotificationsIconAlias;
use Illuminate\Support\ServiceProvider;

/**
 * Filament v4 Service Provider for Solar Icons
 * 
 * This service provider extends the base functionality to provide
 * Filament v4 specific features like icon alias registration
 * and enum-based icon management.
 */
class FilamentV4ServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register the base icons first
        $this->app->register(SolarIconSetServiceProvider::class);
        
        // Register Solar icon aliases for Filament v4
        $this->registerFilamentIconAliases();
        
        // Publish configuration if needed
        $this->publishes([
            __DIR__ . '/../config/solar-icons.php' => config_path('solar-icons.php'),
        ], 'solar-icons-config');
    }

    /**
     * Register Solar icons as replacements for default Filament icons
     */
    protected function registerFilamentIconAliases(): void
    {
        if (!class_exists(FilamentIcon::class)) {
            return; // Filament v4 not available
        }

        // Only register if user has opted in via config
        if (!config('solar-icons.replace_default_icons', false)) {
            return;
        }

        FilamentIcon::register([
            // Panel Icons
            PanelsIconAlias::GLOBAL_SEARCH_FIELD => SolarIcon::LinearSearch->value,
            PanelsIconAlias::PAGES_DASHBOARD_NAVIGATION_ITEM => SolarIcon::Home->value,
            PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON => SolarIcon::OutlineChevronLeft->value,
            PanelsIconAlias::SIDEBAR_EXPAND_BUTTON => SolarIcon::OutlineChevronRight->value,
            PanelsIconAlias::SIDEBAR_GROUP_COLLAPSE_BUTTON => SolarIcon::OutlineChevronUp->value,
            PanelsIconAlias::USER_MENU_PROFILE_ITEM => SolarIcon::OutlineUser->value,
            PanelsIconAlias::USER_MENU_LOGOUT_BUTTON => SolarIcon::OutlineLogout->value,
            
            // Action Icons
            ActionsIconAlias::CREATE_ACTION_GROUPED => SolarIcon::OutlineAdd->value,
            ActionsIconAlias::EDIT_ACTION => SolarIcon::OutlineEdit->value,
            ActionsIconAlias::EDIT_ACTION_GROUPED => SolarIcon::OutlineEdit->value,
            ActionsIconAlias::DELETE_ACTION => SolarIcon::OutlineDelete->value,
            ActionsIconAlias::DELETE_ACTION_GROUPED => SolarIcon::OutlineDelete->value,
            ActionsIconAlias::VIEW_ACTION => SolarIcon::OutlineEye->value,
            ActionsIconAlias::VIEW_ACTION_GROUPED => SolarIcon::OutlineEye->value,
            
            // Form Icons
            FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_ADD => SolarIcon::OutlineAdd->value,
            FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_DELETE => SolarIcon::OutlineDelete->value,
            FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_CLONE => SolarIcon::OutlineCopy->value,
            FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_MOVE_UP => SolarIcon::OutlineArrowUp->value,
            FormsIconAlias::COMPONENTS_BUILDER_ACTIONS_MOVE_DOWN => SolarIcon::OutlineArrowDown->value,
            FormsIconAlias::COMPONENTS_REPEATER_ACTIONS_ADD => SolarIcon::OutlineAdd->value,
            FormsIconAlias::COMPONENTS_REPEATER_ACTIONS_DELETE => SolarIcon::OutlineDelete->value,
            FormsIconAlias::COMPONENTS_REPEATER_ACTIONS_CLONE => SolarIcon::OutlineCopy->value,
            FormsIconAlias::COMPONENTS_REPEATER_ACTIONS_MOVE_UP => SolarIcon::OutlineArrowUp->value,
            FormsIconAlias::COMPONENTS_REPEATER_ACTIONS_MOVE_DOWN => SolarIcon::OutlineArrowDown->value,
            FormsIconAlias::COMPONENTS_TEXT_INPUT_ACTIONS_SHOW_PASSWORD => SolarIcon::OutlineEye->value,
            FormsIconAlias::COMPONENTS_TEXT_INPUT_ACTIONS_HIDE_PASSWORD => SolarIcon::OutlineEyeSlash->value,
            
            // Table Icons
            TablesIconAlias::ACTIONS_FILTER => SolarIcon::OutlineFilter->value,
            TablesIconAlias::SEARCH_FIELD => SolarIcon::LinearSearch->value,
            TablesIconAlias::HEADER_CELL_SORT_ASC_BUTTON => SolarIcon::OutlineArrowUp->value,
            TablesIconAlias::HEADER_CELL_SORT_DESC_BUTTON => SolarIcon::OutlineArrowDown->value,
            TablesIconAlias::COLUMNS_ICON_COLUMN_TRUE => SolarIcon::Success->value,
            TablesIconAlias::COLUMNS_ICON_COLUMN_FALSE => SolarIcon::OutlineRemove->value,
            
            // Notification Icons
            NotificationsIconAlias::NOTIFICATION_SUCCESS => SolarIcon::Success->value,
            NotificationsIconAlias::NOTIFICATION_WARNING => SolarIcon::Warning->value,
            NotificationsIconAlias::NOTIFICATION_DANGER => SolarIcon::Error->value,
            NotificationsIconAlias::NOTIFICATION_INFO => SolarIcon::Info->value,
            NotificationsIconAlias::NOTIFICATION_CLOSE_BUTTON => SolarIcon::OutlineRemove->value,
        ]);
    }

    /**
     * Register additional Solar icon sets for specific use cases
     */
    protected function registerAdditionalIconSets(): void
    {
        // Register additional icon sets if needed
        // This could include custom icon sets or theme-specific icons
    }

    /**
     * Get default configuration
     */
    protected function getDefaultConfig(): array
    {
        return [
            'replace_default_icons' => false,
            'preferred_style' => 'linear',
            'fallback_style' => 'outline',
            'cache_icons' => true,
            'icon_aliases' => [],
        ];
    }
}
