# Solar Icons Package Fixes

This document explains the fixes applied to resolve the logging and Blade component issues.

## Issues Fixed

### 1. Excessive Logging Issue

**Problem**: The package was logging flattening messages for every command execution:
```
Solar Icons: Flattened 1246 files from /path/to/solar-bold to /tmp/solar-icons/all with prefix solar-bold
Solar Icons: Flattened 1215 files from /path/to/solar-bold-duotone to /tmp/solar-icons/all with prefix solar-bold-duotone
...
```

**Root Cause**: 
- The service provider was rebuilding flattened icon structures on every request
- Logging was controlled by `config('app.debug')` which is often `true` in development
- Used `error_log()` which outputs directly to console

**Solution**:
1. **Added configuration control**: New `solar-icons.development.log_flattening` setting
2. **Conditional rebuilding**: Icons are only rebuilt when necessary (directory doesn't exist or is empty)
3. **Better logging**: Uses Laravel's logger when available, falls back to `error_log()`
4. **Performance optimization**: Added `force_rebuild` option for development

### 2. Blade Component Issue

**Problem**: `<x-solar-linear-home class="w-6 h-6" />` was not working.

**Root Cause**: 
- Icon names are case-sensitive
- The actual file is named `Home.svg` (capital H), not `home.svg`
- Multiple `Home.svg` files exist in different subdirectories

**Solution**:
- Documented correct usage with proper case sensitivity
- The correct component name is: `<x-solar-linear-Home class="w-6 h-6" />`

## Configuration Changes

### New Configuration Options

Added to `config/solar-icons.php`:

```php
'development' => [
    // Control flattening operation logging
    'log_flattening' => env('SOLAR_ICONS_LOG_FLATTENING', false),
    
    // Control missing icon warnings
    'log_missing_icons' => env('SOLAR_ICONS_LOG_MISSING', false),
    
    // Force rebuild on every request (development only)
    'force_rebuild' => env('SOLAR_ICONS_FORCE_REBUILD', false),
],
```

### Environment Variables

You can control logging via environment variables:

```env
# Disable all Solar Icons logging (recommended for production)
SOLAR_ICONS_LOG_FLATTENING=false
SOLAR_ICONS_LOG_MISSING=false
SOLAR_ICONS_FORCE_REBUILD=false

# Enable logging for debugging (development only)
SOLAR_ICONS_LOG_FLATTENING=true
SOLAR_ICONS_LOG_MISSING=true
SOLAR_ICONS_FORCE_REBUILD=true
```

## Code Changes

### Service Provider Improvements

1. **Conditional Rebuilding**:
   ```php
   protected function createFlattenedIconSet(string $sourcePath, string $set): ?string
   {
       $tempPath = sys_get_temp_dir() . "/solar-icons/$set";
       
       // Check if we should force rebuild or if directory doesn't exist
       $forceRebuild = config('solar-icons.development.force_rebuild', false);
       $shouldRebuild = $forceRebuild || !is_dir($tempPath) || $this->isDirectoryEmpty($tempPath);
       
       if (!$shouldRebuild) {
           return $tempPath; // Use existing flattened icons
       }
       
       // ... rebuild logic
   }
   ```

2. **Controlled Logging**:
   ```php
   // Only log if explicitly enabled in configuration
   if (config('solar-icons.development.log_flattening', false)) {
       $this->logFlatteningOperation($count, $sourceDir, $targetDir, $setPrefix);
   }
   ```

3. **Better Logging Method**:
   ```php
   protected function logFlatteningOperation(int $count, string $sourceDir, string $targetDir, string $setPrefix): void
   {
       try {
           if (function_exists('logger')) {
               logger()->debug("Solar Icons: Flattened {$count} files...");
           } elseif (function_exists('error_log')) {
               error_log("Solar Icons: Flattened {$count} files...");
           }
       } catch (\Throwable $e) {
           // Silently ignore logging errors
       }
   }
   ```

## Usage Examples

### Correct Blade Component Usage

```blade
<!-- ✅ Correct - note the capital 'H' -->
<x-solar-linear-Home class="w-6 h-6" />
<x-solar-bold-Home class="w-8 h-8 text-blue-500" />
<x-solar-outline-Home />

<!-- ❌ Incorrect - lowercase 'h' won't work -->
<x-solar-linear-home class="w-6 h-6" />
```

### Alternative Usage Methods

```blade
<!-- Using @svg directive -->
@svg('solar-linear-Home', 'w-6 h-6')

<!-- Using x-icon component -->
<x-icon name="solar-linear-Home" class="w-6 h-6" />

<!-- Using SolarIcon enum -->
<x-icon :name="SolarIcon::Home->forSet('solar-linear')" class="w-6 h-6" />
```

### Finding Available Icons

```bash
# Browse available icons
php artisan solar-icons:browse home

# Search for specific icons
php artisan solar-icons:browse --style=linear

# Show enum cases for Filament v4
php artisan solar-icons:browse home --enum
```

## Performance Impact

### Before Fixes
- Icons rebuilt on every request
- Excessive logging output
- Slower command execution

### After Fixes
- Icons built once and cached in temp directory
- Logging only when explicitly enabled
- Faster subsequent requests
- Configurable rebuild behavior for development

## Migration Guide

### For Existing Users

1. **Update configuration** (optional):
   ```bash
   php artisan vendor:publish --tag=solar-icons-config --force
   ```

2. **Fix Blade components** with incorrect casing:
   ```blade
   <!-- Change this -->
   <x-solar-linear-home />
   
   <!-- To this -->
   <x-solar-linear-Home />
   ```

3. **Set environment variables** if you want to control logging:
   ```env
   SOLAR_ICONS_LOG_FLATTENING=false
   ```

### No Breaking Changes

These fixes are backward compatible. Existing code will continue to work, but you may need to fix icon name casing for components that weren't working before.
