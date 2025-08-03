# Solar Icons Troubleshooting Guide

This guide helps resolve common issues when using Solar Icons with Filament and Laravel.

## Common Issues

### 1. "Unable to locate a class or view for component [solar-bold-home]"

**Problem**: BladeUI Icons cannot find the Solar icon component.

**Causes & Solutions**:

#### A. Service Provider Not Registered
```bash
# Check if the service provider is auto-discovered
php artisan package:discover

# If not working, manually register in config/app.php
'providers' => [
    // ...
    Monsefeledrisse\FilamentSolarIcons\SolarIconSetServiceProvider::class,
],
```

#### B. Cache Issues
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Clear icon cache specifically
php artisan icons:clear
```

#### C. Incorrect Icon Names
The enum uses the actual file paths. Use these corrected names:

```php
// ❌ Wrong - these don't exist
SolarIcon::Home->value;  // 'solar-bold-home'

// ✅ Correct - these match actual files
SolarIcon::Home->value;  // 'solar-bold-essentional___ui.home2'
```

### 2. "Svg by name 'solar-bold-home' from set 'default' not found"

**Problem**: BladeUI Icons is looking in the wrong icon set.

**Solution**: Use the correct icon set prefix:

```php
// In Filament components
protected static string $navigationIcon = SolarIcon::Home->value;

// Or use string directly (not recommended)
protected static string $navigationIcon = 'solar-bold-essentional___ui.home2';
```

### 3. Icons Not Displaying in Filament

**Problem**: Icons appear as missing or broken.

**Solutions**:

#### A. Check Icon Set Registration
```php
// Add to a service provider or AppServiceProvider
use BladeUI\Icons\Factory;

public function boot()
{
    $factory = app(Factory::class);
    $iconSets = $factory->all();
    
    // Should include solar-bold, solar-linear, etc.
    dump($iconSets);
}
```

#### B. Verify File Permissions
```bash
# Ensure icon files are readable
chmod -R 644 vendor/monsefeledrisse/filament-solar-icons/resources/icons/
```

#### C. Check Configuration
```bash
# Publish and check configuration
php artisan vendor:publish --tag=solar-icons-config

# Edit config/solar-icons.php to enable/disable icon sets
```

## Correct Usage Examples

### In Filament Resources

```php
<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;
use Monsefeledrisse\FilamentSolarIcons\SolarIcon;

class UserResource extends Resource
{
    // ✅ Correct usage
    protected static ?string $navigationIcon = SolarIcon::User->value;
    
    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->prefixIcon(SolarIcon::User->value),
                
            TextInput::make('email')
                ->prefixIcon(SolarIcon::Mail->value),
        ]);
    }
}
```

### In Blade Templates

```blade
{{-- ✅ Correct usage --}}
<x-icon :name="SolarIcon::Home->value" />

{{-- Or with string (ensure correct name) --}}
<x-solar-bold-essentional___ui.home2 />
```

### Available Icons

Here are the corrected icon names that actually exist:

```php
// Common UI Icons
SolarIcon::Home->value;      // 'solar-bold-essentional___ui.home2'
SolarIcon::User->value;      // 'solar-bold-Users.user_block'  
SolarIcon::Users->value;     // 'solar-bold-Users.users_group_rounded'
SolarIcon::Settings->value;  // 'solar-bold-settings___fine_tuning.settings_minimalistic'
SolarIcon::Search->value;    // 'solar-bold-Search.minimalistic_magnifer'
```

## Debugging Steps

### 1. Verify Package Installation
```bash
composer show monsefeledrisse/filament-solar-icons
```

### 2. Check Service Provider Registration
```bash
php artisan route:list | grep solar
```

### 3. Test Icon Resolution
```php
// In tinker or a test file
use Monsefeledrisse\FilamentSolarIcons\SolarIcon;
use BladeUI\Icons\Factory;

$factory = app(Factory::class);
$iconSets = $factory->all();

// Check if solar icon sets are registered
foreach ($iconSets as $name => $set) {
    if (str_contains($name, 'solar')) {
        echo "Found: {$name} at {$set->path()}\n";
    }
}

// Test specific icon
try {
    $icon = $factory->svg(SolarIcon::Home->value);
    echo "Icon found: " . get_class($icon) . "\n";
} catch (\Exception $e) {
    echo "Icon not found: " . $e->getMessage() . "\n";
}
```

### 4. Check File Structure
```bash
# Verify icon files exist
ls -la vendor/monsefeledrisse/filament-solar-icons/resources/icons/solar/

# Check specific icon
ls -la vendor/monsefeledrisse/filament-solar-icons/resources/icons/solar/solar-bold/essentional,_ui/home2.svg
```

## Configuration Options

### Publish Configuration
```bash
php artisan vendor:publish --tag=solar-icons-config
```

### Customize Icon Sets
```php
// config/solar-icons.php
return [
    'icon_sets' => [
        'solar-bold' => [
            'enabled' => true,
            'path' => 'resources/icons/solar/solar-bold',
        ],
        'solar-linear' => [
            'enabled' => true,
            'path' => 'resources/icons/solar/solar-linear',
        ],
        // Disable unused sets for better performance
        'solar-broken' => [
            'enabled' => false,
        ],
    ],
    
    'cache_icons' => true,
    
    'development' => [
        'log_missing_icons' => true,
    ],
];
```

## Performance Optimization

### 1. Disable Unused Icon Sets
Only enable the icon styles you actually use.

### 2. Enable Icon Caching
```php
// config/solar-icons.php
'cache_icons' => env('SOLAR_ICONS_CACHE', true),
```

### 3. Preload Icons
```bash
# Add to deployment script
php artisan icons:cache
```

## Getting Help

If you're still experiencing issues:

1. **Check Laravel Version**: Ensure compatibility with your Laravel/Filament version
2. **Review Logs**: Check `storage/logs/laravel.log` for detailed error messages
3. **Test in Isolation**: Create a minimal test case to isolate the issue
4. **Report Issues**: Open an issue on GitHub with:
   - Laravel version
   - Filament version
   - Package version
   - Complete error message
   - Steps to reproduce

## Version Information

- **Package Version**: 1.0.1
- **Laravel Support**: 8.x, 9.x, 10.x, 11.x
- **Filament Support**: 3.x, 4.x
- **PHP Requirements**: 8.1+
