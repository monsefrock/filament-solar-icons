<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Replace Default Filament Icons
    |--------------------------------------------------------------------------
    |
    | When enabled, this will replace Filament's default Heroicons with
    | Solar icons throughout the admin panel. This only works with
    | Filament v4 and above.
    |
    */
    'replace_default_icons' => false,

    /*
    |--------------------------------------------------------------------------
    | Preferred Icon Style
    |--------------------------------------------------------------------------
    |
    | The default style to use when no specific style is requested.
    | Available styles: bold, bold-duotone, broken, line-duotone, linear, outline
    |
    */
    'preferred_style' => 'linear',

    /*
    |--------------------------------------------------------------------------
    | Fallback Icon Style
    |--------------------------------------------------------------------------
    |
    | The fallback style to use if an icon is not available in the preferred style.
    |
    */
    'fallback_style' => 'outline',

    /*
    |--------------------------------------------------------------------------
    | Cache Icons
    |--------------------------------------------------------------------------
    |
    | Whether to cache icon discovery and mappings for better performance.
    |
    */
    'cache_icons' => true,

    /*
    |--------------------------------------------------------------------------
    | Custom Icon Aliases
    |--------------------------------------------------------------------------
    |
    | Define custom aliases for frequently used icons. This allows you to
    | use shorter, more semantic names for your icons.
    |
    | Example:
    | 'dashboard' => 'solar-linear-home',
    | 'profile' => 'solar-outline-user',
    |
    */
    'icon_aliases' => [
        // Add your custom aliases here
    ],

    /*
    |--------------------------------------------------------------------------
    | Icon Sets Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for each icon set. You can disable specific sets
    | if you don't need them to reduce memory usage.
    |
    */
    'icon_sets' => [
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
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Tools
    |--------------------------------------------------------------------------
    |
    | Tools and features for development and debugging.
    |
    */
    'development' => [
        'enable_icon_browser' => env('APP_DEBUG', false),
        'log_missing_icons' => env('APP_DEBUG', false),
        'generate_enum_cases' => env('APP_DEBUG', false),
    ],
];
