# Filament Solar Icons

A comprehensive Solar Icon Set package for Filament Admin Panel, providing 6 different icon styles with over 1,000 high-quality SVG icons.

## Features

- **6 Icon Styles**: Bold, Bold Duotone, Broken, Line Duotone, Linear, and Outline
- **1000+ Icons**: Comprehensive collection covering all major categories
- **Laravel Integration**: Seamless integration with Laravel applications
- **BladeUI Icons**: Built on top of the robust BladeUI Icons package
- **Filament Ready**: Optimized for use with Filament Admin Panel

## Installation

Install the package via Composer:

```bash
composer require monsefeledrisse/filament-solar-icons
```

The service provider will be automatically registered via Laravel's package discovery.

## Usage

Once installed, you can use the icons in your Blade templates using the BladeUI Icons syntax:

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

<!-- And so on for all 6 styles -->
```

### Available Icon Sets

- `solar-bold` - Bold style icons
- `solar-bold-duotone` - Bold duotone style icons  
- `solar-broken` - Broken style icons
- `solar-line-duotone` - Line duotone style icons
- `solar-linear` - Linear style icons
- `solar-outline` - Outline style icons

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

## Development

### Requirements

- PHP 7.4 or higher
- Laravel 8.0 or higher
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
- Package created by [Monsef Eledrisse](https://github.com/monsefeledrisse)
