# Solar Icons - Filament Integration Guide

This guide provides comprehensive instructions for integrating Solar icons with both FilamentPHP v3 and v4.

## Table of Contents

1. [Installation](#installation)
2. [Filament v3 Integration](#filament-v3-integration)
3. [Filament v4 Integration](#filament-v4-integration)
4. [Usage Examples](#usage-examples)
5. [Best Practices](#best-practices)
6. [Migration Guide](#migration-guide)

## Installation

Install the package via Composer:

```bash
composer require monsefeledrisse/filament-solar-icons
```

The service provider will be automatically registered via Laravel's package discovery.

## Filament v3 Integration

### Basic Setup

Filament v3 integration works out of the box using BladeUI Icons. No additional configuration is required.

### Available Icon Sets

- `solar-bold` - Bold style icons
- `solar-bold-duotone` - Bold duotone style icons  
- `solar-broken` - Broken style icons
- `solar-line-duotone` - Line duotone style icons
- `solar-linear` - Linear style icons
- `solar-outline` - Outline style icons

### Usage in Filament v3

```php
// Form Fields
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

TextInput::make('name')
    ->prefixIcon('solar-linear-user')
    ->suffixIcon('solar-outline-check-circle'),

Select::make('status')
    ->options([...])
    ->prefixIcon('solar-bold-settings'),

// Table Columns
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

TextColumn::make('name')
    ->icon('solar-linear-user'),

IconColumn::make('is_active')
    ->boolean()
    ->trueIcon('solar-bold-check-circle')
    ->falseIcon('solar-outline-close-circle'),

// Actions
use Filament\Actions\Action;
use Filament\Tables\Actions\EditAction;

Action::make('export')
    ->icon('solar-linear-download')
    ->action(fn () => ...),

EditAction::make()
    ->icon('solar-outline-edit'),

// Navigation
use Filament\Navigation\NavigationItem;

NavigationItem::make('Dashboard')
    ->icon('solar-bold-home')
    ->url('/dashboard'),
```

## Filament v4 Integration

### Enhanced Setup for v4

Filament v4 introduces an enum-based approach for type-safe icon usage, similar to Heroicons.

### Using the SolarIcon Enum

```php
use Monsefeledrisse\FilamentSolarIcons\SolarIcon;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;

// Type-safe icon usage
Action::make('star')
    ->icon(SolarIcon::Star),

TextInput::make('name')
    ->prefixIcon(SolarIcon::OutlineUser),

// IDE autocompletion works!
$icon = SolarIcon::LinearHome; // Your IDE will suggest available icons
```

### Replacing Default Filament Icons

To replace Filament's default Heroicons with Solar icons, publish and configure the config file:

```bash
php artisan vendor:publish --tag=solar-icons-config
```

Then in `config/solar-icons.php`:

```php
return [
    'replace_default_icons' => true,
    'preferred_style' => 'linear',
    'fallback_style' => 'outline',
];
```

### Available Icon Styles in v4

The `SolarIcon` enum provides icons in multiple styles:

- **Bold**: `SolarIcon::Home` (solid, strong visual weight)
- **Outline**: `SolarIcon::OutlineHome` (clean outlined style)
- **Linear**: `SolarIcon::LinearHome` (minimal line style)

## Usage Examples

### Resource Configuration

```php
<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Monsefeledrisse\FilamentSolarIcons\SolarIcon; // v4 only

class UserResource extends Resource
{
    protected static ?string $navigationIcon = 'solar-linear-users'; // v3
    // OR
    protected static ?string $navigationIcon = SolarIcon::LinearUsers->value; // v4

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->prefixIcon('solar-outline-user') // v3
                    // OR
                    ->prefixIcon(SolarIcon::OutlineUser) // v4
                    ->required(),
                    
                TextInput::make('email')
                    ->email()
                    ->prefixIcon('solar-linear-letter') // v3
                    // OR
                    ->prefixIcon(SolarIcon::LinearMail) // v4
                    ->required(),
                    
                Select::make('role')
                    ->options([...])
                    ->prefixIcon('solar-bold-shield-user') // v3
                    // OR
                    ->prefixIcon(SolarIcon::Shield) // v4
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->icon('solar-linear-user') // v3
                    // OR
                    ->icon(SolarIcon::LinearUser) // v4
                    ->searchable(),
                    
                IconColumn::make('is_active')
                    ->boolean()
                    ->trueIcon('solar-bold-check-circle') // v3
                    ->falseIcon('solar-outline-close-circle') // v3
                    // OR
                    ->trueIcon(SolarIcon::Success) // v4
                    ->falseIcon(SolarIcon::OutlineError) // v4
            ])
            ->actions([
                EditAction::make()
                    ->icon('solar-outline-edit'), // v3
                    // OR
                    ->icon(SolarIcon::OutlineEdit), // v4
                    
                DeleteAction::make()
                    ->icon('solar-outline-trash-bin-minimalistic'), // v3
                    // OR
                    ->icon(SolarIcon::OutlineDelete), // v4
            ]);
    }
}
```

### Custom Actions with Solar Icons

```php
// Filament v3
Action::make('export_users')
    ->label('Export Users')
    ->icon('solar-linear-download')
    ->action(function () {
        // Export logic
    }),

Action::make('send_notification')
    ->label('Send Notification')
    ->icon('solar-bold-bell-bing')
    ->action(function () {
        // Notification logic
    }),

// Filament v4
Action::make('export_users')
    ->label('Export Users')
    ->icon(SolarIcon::LinearDownload)
    ->action(function () {
        // Export logic
    }),

Action::make('send_notification')
    ->label('Send Notification')
    ->icon(SolarIcon::Notification)
    ->action(function () {
        // Notification logic
    }),
```

### Navigation Menu

```php
// In your PanelProvider

// Filament v3
->navigationItems([
    NavigationItem::make('Analytics')
        ->icon('solar-bold-chart')
        ->url('/analytics'),
        
    NavigationItem::make('Reports')
        ->icon('solar-linear-document-text')
        ->url('/reports'),
        
    NavigationItem::make('Settings')
        ->icon('solar-outline-settings')
        ->url('/settings'),
])

// Filament v4
->navigationItems([
    NavigationItem::make('Analytics')
        ->icon(SolarIcon::Chart)
        ->url('/analytics'),
        
    NavigationItem::make('Reports')
        ->icon(SolarIcon::LinearFile)
        ->url('/reports'),
        
    NavigationItem::make('Settings')
        ->icon(SolarIcon::OutlineSettings)
        ->url('/settings'),
])
```

### Widget Icons

```php
// Stats Overview Widget

// Filament v3
Stat::make('Total Users', $totalUsers)
    ->icon('solar-bold-users')
    ->color('success'),

Stat::make('Revenue', $revenue)
    ->icon('solar-linear-dollar-minimalistic')
    ->color('primary'),

// Filament v4
Stat::make('Total Users', $totalUsers)
    ->icon(SolarIcon::Users)
    ->color('success'),

Stat::make('Revenue', $revenue)
    ->icon(SolarIcon::LinearMoney)
    ->color('primary'),
```

## Best Practices

### Icon Naming Convention

1. **Use semantic names**: Choose icons that clearly represent their function
2. **Be consistent**: Stick to one style throughout your application
3. **Consider context**: Use appropriate visual weight (bold vs outline) based on importance

### Style Guidelines

- **Linear**: Best for clean, modern interfaces
- **Outline**: Good for secondary actions and subtle UI elements
- **Bold**: Use for primary actions and important status indicators
- **Duotone**: Great for illustrations and decorative elements

### Performance Tips

1. **Disable unused icon sets** in the configuration to reduce memory usage
2. **Use caching** for icon discovery in production
3. **Prefer enum usage** in v4 for better IDE support and type safety

### Accessibility

Always provide meaningful labels for icon-only buttons:

```php
Action::make('delete')
    ->icon(SolarIcon::OutlineDelete)
    ->label('Delete User') // Important for screen readers
    ->tooltip('Delete this user permanently'),
```

## Migration Guide

### From Heroicons to Solar Icons

1. **Identify current icons**: List all Heroicons used in your application
2. **Find Solar equivalents**: Use the mapping table below
3. **Update gradually**: Migrate one component type at a time
4. **Test thoroughly**: Ensure all icons display correctly

### Common Icon Mappings

| Heroicon | Solar Icon (v3) | Solar Icon (v4) |
|----------|-----------------|-----------------|
| `heroicon-o-home` | `solar-linear-home` | `SolarIcon::LinearHome` |
| `heroicon-o-user` | `solar-outline-user` | `SolarIcon::OutlineUser` |
| `heroicon-o-cog-6-tooth` | `solar-linear-settings` | `SolarIcon::LinearSettings` |
| `heroicon-o-bell` | `solar-outline-bell` | `SolarIcon::OutlineBell` |
| `heroicon-o-calendar` | `solar-linear-calendar` | `SolarIcon::LinearCalendar` |
| `heroicon-o-pencil` | `solar-outline-pen` | `SolarIcon::OutlineEdit` |
| `heroicon-o-trash` | `solar-outline-trash-bin-minimalistic` | `SolarIcon::OutlineDelete` |
| `heroicon-o-eye` | `solar-outline-eye` | `SolarIcon::OutlineEye` |
| `heroicon-o-heart` | `solar-outline-heart` | `SolarIcon::OutlineHeart` |
| `heroicon-o-star` | `solar-outline-star` | `SolarIcon::OutlineStar` |

### Automated Migration (v4 only)

For Filament v4, you can use the helper class to find and replace icons:

```php
use Monsefeledrisse\FilamentSolarIcons\SolarIconHelper;

// Search for similar icons
$suggestions = SolarIconHelper::searchIcons('user');

// Get recommendations by context
$navigationIcons = SolarIconHelper::getIconRecommendations('navigation');
```

## Troubleshooting

### Icons Not Displaying

1. **Check icon name**: Ensure the icon name is correct and exists
2. **Verify style**: Make sure the icon exists in the specified style
3. **Clear cache**: Run `php artisan cache:clear` if using icon caching
4. **Check file permissions**: Ensure icon files are readable

### Performance Issues

1. **Disable unused icon sets** in configuration
2. **Enable icon caching** in production
3. **Use CDN** for icon assets if needed

### IDE Support

For better IDE support in v4:

1. **Use the enum**: Always prefer `SolarIcon::IconName` over string literals
2. **Install IDE plugins**: Use plugins that support PHP enums
3. **Generate PHPDoc**: Use the helper to generate documentation

## Support

For issues, feature requests, or contributions:

- **GitHub**: [https://github.com/monsefeledrisse/filament-solar-icons](https://github.com/monsefeledrisse/filament-solar-icons)
- **Documentation**: This file and inline code comments
- **Community**: Filament Discord community

## License

This package is open-sourced software licensed under the MIT license.
