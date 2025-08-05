<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Classes
    |--------------------------------------------------------------------------
    |
    | This config option allows you to set default classes that will be applied
    | to all Solar icons. You can override this on a per-icon basis by passing
    | the class attribute directly to the icon component.
    |
    */

    'class' => '',

    /*
    |--------------------------------------------------------------------------
    | Default Attributes
    |--------------------------------------------------------------------------
    |
    | This config option allows you to set default attributes that will be
    | applied to all Solar icons. You can override this on a per-icon basis by
    | passing the attribute directly to the icon component.
    |
    */

    'attributes' => [
        // 'width' => 50,
        // 'height' => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Icon Sets
    |--------------------------------------------------------------------------
    |
    | This config option allows you to define which icon sets should be
    | registered. By default, all Solar icon sets are registered.
    |
    */

    'sets' => [
        'solar-bold',
        'solar-bold-duotone',
        'solar-broken',
        'solar-line-duotone',
        'solar-linear',
        'solar-outline',
    ],

    /*
    |--------------------------------------------------------------------------
    | Development Settings
    |--------------------------------------------------------------------------
    |
    | These settings control development and debugging features.
    |
    */

    'development' => [
        /*
        |--------------------------------------------------------------------------
        | Enable Logging
        |--------------------------------------------------------------------------
        |
        | When enabled, the package will log icon flattening operations and other
        | debug information. This should be disabled in production.
        |
        */
        'log_flattening' => env('SOLAR_ICONS_LOG_FLATTENING', false),

        /*
        |--------------------------------------------------------------------------
        | Log Missing Icons
        |--------------------------------------------------------------------------
        |
        | When enabled, the package will log warnings when requested icons
        | or icon sets are not found.
        |
        */
        'log_missing_icons' => env('SOLAR_ICONS_LOG_MISSING', false),

        /*
        |--------------------------------------------------------------------------
        | Force Rebuild
        |--------------------------------------------------------------------------
        |
        | When enabled, the package will always rebuild the flattened icon
        | structure on every request. Disable in production for better performance.
        |
        */
        'force_rebuild' => env('SOLAR_ICONS_FORCE_REBUILD', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    |
    | These settings control performance optimizations for the icon package.
    |
    */

    'performance' => [
        /*
        |--------------------------------------------------------------------------
        | Enable Caching
        |--------------------------------------------------------------------------
        |
        | When enabled, icon metadata will be cached to improve performance.
        | Recommended for production environments.
        |
        */
        'cache_enabled' => env('SOLAR_ICONS_CACHE_ENABLED', true),

        /*
        |--------------------------------------------------------------------------
        | Cache TTL
        |--------------------------------------------------------------------------
        |
        | Time-to-live for cached icon data in seconds. Default is 1 hour.
        |
        */
        'cache_ttl' => env('SOLAR_ICONS_CACHE_TTL', 3600),

        /*
        |--------------------------------------------------------------------------
        | Lazy Loading
        |--------------------------------------------------------------------------
        |
        | When enabled, icon sets are only loaded when first accessed.
        | This significantly improves application boot time.
        |
        */
        'lazy_loading' => env('SOLAR_ICONS_LAZY_LOADING', true),

        /*
        |--------------------------------------------------------------------------
        | Preload Only Used Sets
        |--------------------------------------------------------------------------
        |
        | Only preload icon sets that are commonly used. Other sets will be
        | loaded on-demand. Leave empty to load all sets.
        |
        */
        'preload_sets' => env('SOLAR_ICONS_PRELOAD_SETS', 'solar-outline,solar-linear'),
    ],
];
