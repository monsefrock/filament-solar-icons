# Laravel Solar Icons

A comprehensive Solar Icon Set package for Laravel applications, providing 6 different icon styles with over 1,000 high-quality SVG icons. Built on top of the robust BladeUI Icons package.

## Features

- **🎨 6 Icon Styles**: Bold, Bold Duotone, Broken, Line Duotone, Linear, and Outline
- **📦 1000+ Icons**: Comprehensive collection covering all major categories
- **🔧 Laravel Integration**: Seamless integration with Laravel applications
- **⚡ BladeUI Icons**: Built on top of the robust BladeUI Icons package
- **🔒 Type Safety**: Enum-based icon system with IDE autocompletion
- **🎛️ Flexible Usage**: Works with Blade components, helper functions, and direct SVG usage
- **⚙️ Configurable**: Customizable icon sets, classes, and attributes
- **🔍 Developer Tools**: Icon browser, search, and helper utilities

## Installation

Install the package via Composer:

```bash
composer require monsefeledrisse/laravel-solar-icons
```

The service provider will be automatically registered via Laravel's package discovery.

## Quick Start

### Using Blade Components

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

<!-- With custom attributes -->
<x-solar-linear-home class="w-6 h-6 text-blue-500" />
```

### Using the Icon Helper

```blade
<!-- Using the @svg directive -->
@svg('solar-linear-home', 'w-6 h-6')
@svg('solar-bold-user', ['class' => 'text-gray-500'])

<!-- Using the icon component -->
<x-icon name="solar-outline-settings" class="w-5 h-5" />
```

### Using the SolarIcon Enum (Type-Safe)

```php
use Monsefeledrisse\LaravelSolarIcons\SolarIcon;

// In your Blade views
<x-icon :name="SolarIcon::Home->value" />

// In your PHP code
$iconName = SolarIcon::User->value; // Returns 'solar-bold-User'

// Get available sets for an icon
$sets = SolarIcon::Home->getAvailableSets();

// Get icon for specific set
$linearHome = SolarIcon::Home->forSet('solar-linear');
```

## Configuration

Publish the configuration file to customize the package:

```bash
php artisan vendor:publish --tag=solar-icons-config
```

Configure in `config/solar-icons.php`:

```php
return [
    // Default CSS class for all icons
    'class' => '',

    // Default attributes for all icons
    'attributes' => [
        // 'width' => 24,
        // 'height' => 24,
    ],

    // Icon sets to register (remove unused sets for better performance)
    'sets' => [
        'solar-bold',
        'solar-bold-duotone',
        'solar-broken',
        'solar-line-duotone',
        'solar-linear',
        'solar-outline',
    ],
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

## Usage Examples

### In Laravel Views

```blade
<!-- Basic usage -->
<x-solar-linear-home class="w-6 h-6" />

<!-- With Tailwind CSS -->
<div class="flex items-center space-x-2">
    <x-solar-outline-user class="w-5 h-5 text-gray-500" />
    <span>User Profile</span>
</div>

<!-- Using @svg directive -->
@svg('solar-bold-settings', 'w-8 h-8 text-blue-600')

<!-- Dynamic icon selection -->
@php
    $iconName = $user->isActive() ? 'solar-linear-check-circle' : 'solar-outline-close-circle';
@endphp
<x-icon :name="$iconName" class="w-4 h-4" />
```

### In Laravel Components

```php
<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Monsefeledrisse\LaravelSolarIcons\SolarIcon;

class StatusBadge extends Component
{
    public function __construct(
        public string $status,
        public string $label
    ) {}

    public function render()
    {
        $icon = match($this->status) {
            'active' => SolarIcon::LinearCheckCircle->value,
            'inactive' => SolarIcon::OutlineCloseCircle->value,
            'pending' => SolarIcon::LinearClock->value,
            default => SolarIcon::OutlineQuestion->value,
        };

        return view('components.status-badge', compact('icon'));
    }
}
```

### In Controllers

```php
<?php

namespace App\Http\Controllers;

use Monsefeledrisse\LaravelSolarIcons\SolarIcon;
use Monsefeledrisse\LaravelSolarIcons\SolarIconHelper;

class DashboardController extends Controller
{
    public function index()
    {
        $menuItems = [
            [
                'label' => 'Dashboard',
                'icon' => SolarIcon::Home->value,
                'url' => route('dashboard')
            ],
            [
                'label' => 'Users',
                'icon' => SolarIcon::Users->value,
                'url' => route('users.index')
            ],
            [
                'label' => 'Settings',
                'icon' => SolarIcon::Settings->value,
                'url' => route('settings')
            ],
        ];

        return view('dashboard', compact('menuItems'));
    }
}
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

| Heroicon | Solar Icon | SolarIcon Enum |
|----------|------------|----------------|
| `heroicon-o-home` | `solar-linear-Home` | `SolarIcon::Home->forSet('solar-linear')` |
| `heroicon-o-user` | `solar-outline-User` | `SolarIcon::User->forSet('solar-outline')` |
| `heroicon-o-cog-6-tooth` | `solar-linear-Settings` | `SolarIcon::Settings->forSet('solar-linear')` |
| `heroicon-o-bell` | `solar-outline-Notification` | `SolarIcon::Bell->forSet('solar-outline')` |
| `heroicon-o-calendar` | `solar-linear-Calendar` | `SolarIcon::Calendar->forSet('solar-linear')` |
| `heroicon-o-pencil` | `solar-outline-Pen` | `SolarIcon::Pen->forSet('solar-outline')` |
| `heroicon-o-trash` | `solar-outline-trash_bin_minimalistic` | `SolarIcon::TrashBinMinimalistic->forSet('solar-outline')` |

### Helper Tools

```php
use Monsefeledrisse\LaravelSolarIcons\SolarIconHelper;

// Search for icons
$suggestions = SolarIconHelper::searchIcons('user');

// Get all available icon files
$allIcons = SolarIconHelper::getAllIconFiles();

// Get icons by category
$navigationIcons = SolarIconHelper::getIconsByCategory('navigation');
```

## Troubleshooting

### Icons Not Displaying
1. Check icon name spelling and ensure it exists
2. Verify the icon style is available
3. Clear cache: `php artisan cache:clear`
4. Check file permissions on icon directories

### Performance Optimization
1. Disable unused icon sets in configuration
2. Use specific icon sets instead of loading all sets
3. Consider using SVG sprites for frequently used icons

## Development

### Requirements

- PHP 8.1 or higher
- Laravel 9.0 or higher
- BladeUI Icons 1.8 or higher

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
- Package created by [Monsef Eledrisse](https://github.com/monsefrock)
