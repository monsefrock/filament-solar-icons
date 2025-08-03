# Solar Icons Integration Testing Guide

This guide provides comprehensive testing tools to verify that the Filament Solar Icons package is working correctly with the blade-ui-kit/blade-icons package in Laravel applications.

## Overview

The testing suite includes:
- **Automated Tests**: PHPUnit/Pest tests for programmatic verification
- **Web Interface**: Visual testing page accessible via browser
- **Console Commands**: CLI tools for quick integration testing
- **API Endpoints**: JSON endpoints for integration verification

## Quick Start

### 1. Run Automated Tests

```bash
# Run all integration tests
./vendor/bin/pest tests/BladeIconsIntegrationTest.php

# Run with verbose output
./vendor/bin/pest tests/BladeIconsIntegrationTest.php --verbose
```

### 2. Use Console Command

```bash
# Quick integration test
php artisan solar-icons:test-integration

# Detailed test with verbose output
php artisan solar-icons:test-integration --verbose

# Test specific icon
php artisan solar-icons:test-integration --icon="solar-bold-Home"

# Quick test (skip rendering tests)
php artisan solar-icons:test-integration --quick
```

### 3. Access Web Interface

```bash
# Start Laravel development server
php artisan serve

# Visit the test page
http://localhost:8000/solar-icons/test
```

## Test Components

### Automated Tests (`tests/BladeIconsIntegrationTest.php`)

Comprehensive PHPUnit/Pest tests covering:

- **Icon Factory Registration**: Verifies Solar icon sets are registered with BladeUI Icons
- **Blade Component Integration**: Tests `<x-icon name="solar-bold-Home" />` syntax
- **SVG Directive Integration**: Tests `@svg('solar-bold-Home')` directive
- **Enum Integration**: Tests SolarIcon enum values with Blade components
- **Error Handling**: Verifies graceful handling of invalid icons
- **Performance**: Tests rendering speed and efficiency
- **Content Validation**: Ensures valid SVG output

### Web Interface (`/solar-icons/test`)

Interactive testing page featuring:

- **Style Testing**: Visual display of icons across all styles (bold, outline, linear, broken)
- **Component Testing**: Tests both `<x-icon>` and `@svg()` approaches
- **Enum Integration**: Tests SolarIcon enum cases
- **Error Handling**: Shows how invalid icons are handled
- **Interactive Testing**: Input field to test any icon name
- **Status Dashboard**: Shows registration status and statistics

### Console Command (`solar-icons:test-integration`)

CLI testing tool with options:

```bash
# Basic usage
php artisan solar-icons:test-integration

# Available options
--verbose    # Show detailed output
--quick      # Skip rendering tests (faster)
--icon=NAME  # Test specific icon
```

### API Endpoints

JSON endpoints for programmatic testing:

- `GET /solar-icons/test/health` - Health check and status
- `GET /solar-icons/test/icon-sets` - Icon sets information
- `GET /solar-icons/test/enum` - Enum information
- `POST /solar-icons/test/icon` - Test specific icon rendering

## Test Scenarios

### 1. Basic Integration Test

```php
// Test that Solar icons are registered
$factory = app(\BladeUI\Icons\Factory::class);
$registeredSets = $factory->all();
expect($registeredSets)->toHaveKey('solar-bold');
```

### 2. Blade Component Test

```php
// Test Blade component rendering
$html = Blade::render('<x-icon name="solar-bold-Home" />');
expect($html)->toContain('<svg');
expect($html)->toContain('</svg>');
```

### 3. SVG Directive Test

```php
// Test @svg directive
$html = Blade::render("@svg('solar-linear-User')");
expect($html)->toContain('<svg');
```

### 4. Enum Integration Test

```php
// Test enum values
$enumIcon = SolarIcon::FacemaskCircle;
$html = Blade::render('<x-icon name="' . $enumIcon->value . '" />');
expect($html)->toContain('<svg');
```

### 5. Error Handling Test

```php
// Test invalid icon handling
$html = Blade::render('<x-icon name="solar-bold-NonExistent" />');
// Should not throw exception
expect($html)->toBeString();
```

## Troubleshooting

### Common Issues

#### 1. "Unable to locate a class or view for component [solar-bold-home]"

**Cause**: BladeUI Icons cannot find the Solar icon component.

**Solutions**:
```bash
# Check service provider registration
php artisan package:discover

# Verify icon sets are registered
php artisan solar-icons:test-integration --verbose

# Check configuration
php artisan config:cache
```

#### 2. Icons not rendering

**Cause**: Icon files not found or service provider not loaded.

**Solutions**:
```bash
# Check if icon files exist
ls -la resources/icons/solar/

# Verify service provider is loaded
php artisan solar-icons:test-integration

# Check Laravel logs
tail -f storage/logs/laravel.log
```

#### 3. Enum cases not working

**Cause**: Enum not synchronized with actual icon files.

**Solutions**:
```bash
# Synchronize enum with icon files
php bin/sync-solar-icons.php

# Verify enum cases
php artisan solar-icons:test-integration --verbose
```

### Debug Steps

1. **Check Health Endpoint**:
   ```bash
   curl http://localhost:8000/solar-icons/test/health
   ```

2. **Run Console Test**:
   ```bash
   php artisan solar-icons:test-integration --verbose
   ```

3. **Check Laravel Logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Verify File Structure**:
   ```bash
   find resources/icons/solar -name "*.svg" | head -10
   ```

## Integration with CI/CD

### GitHub Actions Example

```yaml
name: Solar Icons Integration Test

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
      - name: Install Dependencies
        run: composer install
      - name: Run Integration Tests
        run: |
          ./vendor/bin/pest tests/BladeIconsIntegrationTest.php
          php artisan solar-icons:test-integration
```

### Laravel Dusk Integration

```php
// Test in browser
$this->browse(function (Browser $browser) {
    $browser->visit('/solar-icons/test')
            ->assertSee('Solar Icons Integration Test')
            ->assertPresent('svg')
            ->assertSee('✓ Success');
});
```

## Performance Testing

### Rendering Speed Test

```php
$icons = ['solar-bold-Home', 'solar-linear-User', 'solar-outline-Star'];
$startTime = microtime(true);

foreach ($icons as $iconName) {
    Blade::render('<x-icon name="' . $iconName . '" />');
}

$duration = microtime(true) - $startTime;
expect($duration)->toBeLessThan(1.0); // Should render in < 1 second
```

### Memory Usage Test

```php
$memoryBefore = memory_get_usage();

// Render 100 icons
for ($i = 0; $i < 100; $i++) {
    Blade::render('<x-icon name="solar-bold-Home" />');
}

$memoryAfter = memory_get_usage();
$memoryUsed = $memoryAfter - $memoryBefore;

expect($memoryUsed)->toBeLessThan(10 * 1024 * 1024); // < 10MB
```

## Custom Test Setup

### Adding Custom Tests

1. **Create Test File**:
   ```php
   // tests/CustomSolarIconTest.php
   use Monsefeledrisse\FilamentSolarIcons\SolarIcon;
   
   it('can render custom icon set', function () {
       $html = Blade::render('<x-icon name="solar-bold-CustomIcon" />');
       expect($html)->toContain('<svg');
   });
   ```

2. **Add to Test Suite**:
   ```bash
   ./vendor/bin/pest tests/CustomSolarIconTest.php
   ```

### Custom Web Routes

```php
// routes/web.php
Route::get('/my-solar-test', function () {
    return view('my-solar-test', [
        'icons' => ['solar-bold-Home', 'solar-linear-User']
    ]);
});
```

## Best Practices

1. **Always test in multiple environments** (local, staging, production)
2. **Use both automated and visual testing**
3. **Test error scenarios** as well as success cases
4. **Monitor performance** with large icon sets
5. **Keep tests updated** when adding new icons
6. **Use the synchronization script** to maintain consistency

## Support

If you encounter issues:

1. Check this testing guide
2. Run the diagnostic commands
3. Review Laravel logs
4. Check the project's GitHub issues
5. Refer to the main package documentation

The testing suite provides comprehensive coverage to ensure Solar Icons work correctly in your Laravel application with blade-ui-kit/blade-icons integration.
