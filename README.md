# Filament Solar Icons

A comprehensive Solar Icon Set package for Filament Admin Panel, providing 6 different icon styles with over 1,000 high-quality SVG icons. Compatible with both **Filament v3** and **Filament v4**.

## Features

- **🎨 6 Icon Styles**: Bold, Bold Duotone, Broken, Line Duotone, Linear, and Outline
- **📦 1000+ Icons**: Comprehensive collection covering all major categories
- **🔧 Laravel Integration**: Seamless integration with Laravel applications
- **⚡ BladeUI Icons**: Built on top of the robust BladeUI Icons package
- **🎯 Filament Ready**: Optimized for use with Filament Admin Panel
- **🔒 Type Safety**: Enum-based icon system for Filament v4 with IDE autocompletion
- **🎛️ Flexible Usage**: Works with both string-based (v3) and enum-based (v4) approaches
- **⚙️ Configurable**: Replace default Filament icons with Solar icons
- **🔍 Developer Tools**: Icon browser, search, and helper utilities

## Installation

Install the package via Composer:

```bash
composer require monsefeledrisse/filament-solar-icons
```

The service provider will be automatically registered via Laravel's package discovery.

## Quick Start

### Filament v3 Usage

```php
// Form Fields
TextInput::make('name')
    ->prefixIcon('solar-linear-user'),

// Table Columns
TextColumn::make('status')
    ->icon('solar-bold-check-circle'),

// Actions
Action::make('export')
    ->icon('solar-outline-download'),

// Navigation
NavigationItem::make('Dashboard')
    ->icon('solar-linear-home'),
```

### Filament v4 Usage (Type-Safe)

```php
use Monsefeledrisse\FilamentSolarIcons\SolarIcon;

// Form Fields with IDE autocompletion
TextInput::make('name')
    ->prefixIcon(SolarIcon::LinearUser),

// Table Columns
TextColumn::make('status')
    ->icon(SolarIcon::Success),

// Actions
Action::make('export')
    ->icon(SolarIcon::OutlineDownload),

// Navigation
NavigationItem::make('Dashboard')
    ->icon(SolarIcon::LinearHome),
```

### Blade Templates

```blade
<!-- Solar Bold Icons -->
<x-solar-bold-home />
<x-solar-bold-user />

<!-- Solar Linear Icons -->
<x-solar-linear-settings />
<x-solar-linear-search />

<!-- Solar Outline Icons -->
<x-solar-outline-calendar />
<x-solar-outline-bell />
```

## Filament v4 Configuration

For enhanced Filament v4 integration, publish the configuration file:

```bash
php artisan vendor:publish --tag=solar-icons-config
```

Configure in `config/solar-icons.php`:

```php
return [
    // Replace default Filament icons with Solar icons
    'replace_default_icons' => true,

    // Preferred icon style
    'preferred_style' => 'linear',

    // Fallback style if preferred is not available
    'fallback_style' => 'outline',

    // Enable caching for better performance
    'cache_icons' => true,
];
```

### Available Icon Sets

- `solar-bold` - Bold style icons (strong visual weight)
- `solar-bold-duotone` - Bold duotone style icons (two-tone bold)
- `solar-broken` - Broken style icons (stylized broken lines)
- `solar-line-duotone` - Line duotone style icons (two-tone lines)
- `solar-linear` - Linear style icons (clean, minimal)
- `solar-outline` - Outline style icons (clean outlined)

## Icon Categories

The Solar icon set includes icons from the following categories:

- Arrows & Navigation
- Astronomy & Science
- Building & Infrastructure
- Business & Statistics
- Communication & Call
- Design & Tools
- Electronic Devices
- Essential UI Elements
- Faces & Emotions
- Files & Documents
- Food & Drinks
- Hands & Gestures
- Home & Garden
- Like & Dislike
- Maps & Location
- Medicine & Health
- Messages & Chat
- Money & Finance
- Music & Audio
- Nature & Travel
- Network & Programming
- Notes & Text
- Notifications
- School & Education
- Search
- Security
- Settings
- Shopping & E-commerce
- Sports & Fitness
- Text Formatting
- Time & Calendar
- Transport & Vehicles
- Users & People
- Video & Media
- Weather

## Comprehensive Examples

### Resource Integration

```php
<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Monsefeledrisse\FilamentSolarIcons\SolarIcon;

class UserResource extends Resource
{
    // v3: protected static ?string $navigationIcon = 'solar-linear-users';
    // v4:
    protected static ?string $navigationIcon = SolarIcon::LinearUsers->value;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->prefixIcon(SolarIcon::OutlineUser) // v4
                ->required(),

            TextInput::make('email')
                ->email()
                ->prefixIcon(SolarIcon::LinearMail) // v4
                ->required(),

            Select::make('role')
                ->options([...])
                ->prefixIcon(SolarIcon::Shield) // v4
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->icon(SolarIcon::LinearUser), // v4

                IconColumn::make('is_active')
                    ->boolean()
                    ->trueIcon(SolarIcon::Success) // v4
                    ->falseIcon(SolarIcon::OutlineError), // v4
            ])
            ->actions([
                EditAction::make()
                    ->icon(SolarIcon::OutlineEdit), // v4

                DeleteAction::make()
                    ->icon(SolarIcon::OutlineDelete), // v4
            ]);
    }
}
```

### Widget Examples

```php
// Stats Overview Widget
Stat::make('Total Users', $totalUsers)
    ->icon(SolarIcon::Users) // v4
    ->color('success'),

Stat::make('Revenue', $revenue)
    ->icon(SolarIcon::LinearMoney) // v4
    ->color('primary'),
```

## Icon Style Guide

- **Linear**: Perfect for clean, modern interfaces
- **Outline**: Great for secondary actions and subtle elements
- **Bold**: Use for primary actions and important status indicators
- **Duotone**: Excellent for illustrations and decorative elements

## Testing

This package includes a comprehensive test suite built with Pest PHP:

```bash
# Install dev dependencies
composer install --dev

# Run tests
composer test

# Or run Pest directly
./vendor/bin/pest
```

### Test Coverage

The test suite covers:

- Service provider registration and boot process
- Icon set registration with correct prefixes and paths
- Directory structure validation
- BladeUI Icons Factory integration
- Error handling and edge cases
- Performance considerations
- Memory management

## Migration from Heroicons

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

### Helper Tools (v4 only)

```php
use Monsefeledrisse\FilamentSolarIcons\SolarIconHelper;

// Search for icons
$suggestions = SolarIconHelper::searchIcons('user');

// Get recommendations by context
$navigationIcons = SolarIconHelper::getIconRecommendations('navigation');

// Convert between styles
$outlineIcon = SolarIconHelper::convertIconStyle('solar-linear-home', 'outline');
```

## Troubleshooting

### Icons Not Displaying
1. Check icon name spelling and ensure it exists
2. Verify the icon style is available
3. Clear cache: `php artisan cache:clear`
4. Check file permissions on icon directories

### Performance Optimization
1. Disable unused icon sets in configuration
2. Enable icon caching in production
3. Use the enum approach in v4 for better performance

## Development

### Requirements

- PHP 8.1 or higher
- Laravel 9.0 or higher
- BladeUI Icons 1.8 or higher
- Filament 3.0+ or 4.0+ (optional)

### Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Run the test suite (`composer test`)
5. Commit your changes (`git commit -m 'Add some amazing feature'`)
6. Push to the branch (`git push origin feature/amazing-feature`)
7. Open a Pull Request

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Credits

- Solar Icons by [480 Design](https://www.figma.com/community/file/1166831539721848736)
- Built on [BladeUI Icons](https://github.com/blade-ui-kit/blade-icons)
- Package created by [Monsef Eledrisse](https://github.com/monsefeledrisse)
