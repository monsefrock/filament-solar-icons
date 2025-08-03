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
];
