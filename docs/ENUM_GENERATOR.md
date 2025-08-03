# Solar Icon Enum Generator

This document describes the automated Solar Icon Enum Generator script that ensures consistency between the `SolarIcon` enum and the actual SVG files in the package.

## Overview

The `bin/generate-solar-enum.php` script automatically generates the `src/SolarIcon.php` file by scanning all Solar icon directories and creating proper PHP enum cases with correct string values that match the actual SVG filenames.

## Features

### 🔍 **Automatic Icon Discovery**
- Recursively scans all Solar icon directories (`resources/icons/solar/solar-*`)
- Finds all SVG files across all 6 icon sets (bold, bold-duotone, broken, line-duotone, linear, outline)
- Handles nested directory structures automatically

### 📝 **Smart Enum Generation**
- Converts filenames to valid PHP enum case names (PascalCase)
- Preserves original filenames as enum values
- Handles special characters, numbers, and edge cases in filenames
- Ensures enum case names are valid PHP identifiers

### 🎯 **Consistency Assurance**
- Maps each unique icon to its corresponding BladeUI Icons identifier
- Tracks which icon sets contain each icon
- Provides helper methods for icon set management
- Eliminates manual errors and inconsistencies

### 🚀 **Future-Proof Automation**
- When new icons are added, simply run the script to update the enum
- No manual maintenance required
- Preserves all existing functionality while adding new icons

## Usage

### Basic Usage

```bash
# Run the generator script
php bin/generate-solar-enum.php

# Or make it executable and run directly
chmod +x bin/generate-solar-enum.php
./bin/generate-solar-enum.php
```

### Output Example

```
🔍 Scanning Solar icon directories...
   Found 1235 icons in solar-bold
   Found 1205 icons in solar-bold-duotone
   Found 1183 icons in solar-broken
   Found 1205 icons in solar-line-duotone
   Found 1235 icons in solar-linear
   Found 1205 icons in solar-outline
📝 Generating enum cases...
💾 Writing SolarIcon.php file...
✅ Successfully generated SolarIcon enum with 1235 cases!
📊 Icon distribution:
   solar-bold: 1235 icons
   solar-bold-duotone: 1205 icons
   solar-broken: 1183 icons
   solar-line-duotone: 1205 icons
   solar-linear: 1235 icons
   solar-outline: 1205 icons
```

## Generated Enum Structure

### Enum Cases

Each enum case follows this pattern:

```php
case FacemaskCircle = 'solar-bold-facemask_circle'; // facemask_circle (available in: solar-bold, solar-bold-duotone, solar-broken, solar-line-duotone, solar-linear, solar-outline)
```

- **Case Name**: `FacemaskCircle` (PascalCase, valid PHP identifier)
- **Value**: `'solar-bold-facemask_circle'` (BladeUI Icons identifier)
- **Comment**: Shows original filename and available icon sets

### Helper Methods

The generated enum includes several helper methods:

#### `getAvailableSets(): array`
Returns all icon sets that contain this icon.

```php
$sets = SolarIcon::FacemaskCircle->getAvailableSets();
// Returns: ['solar-bold', 'solar-bold-duotone', 'solar-broken', 'solar-line-duotone', 'solar-linear', 'solar-outline']
```

#### `getIconName(): string`
Returns the icon name without the set prefix.

```php
$name = SolarIcon::FacemaskCircle->getIconName();
// Returns: 'facemask_circle'
```

#### `getPrimarySet(): string`
Returns the primary icon set (first available set).

```php
$set = SolarIcon::FacemaskCircle->getPrimarySet();
// Returns: 'solar-bold'
```

#### `isAvailableIn(string $set): bool`
Checks if the icon is available in a specific set.

```php
$available = SolarIcon::FacemaskCircle->isAvailableIn('solar-linear');
// Returns: true
```

#### `forSet(string $set): string`
Returns the icon identifier for a specific set.

```php
$identifier = SolarIcon::FacemaskCircle->forSet('solar-linear');
// Returns: 'solar-linear-facemask_circle'
```

## Naming Convention Handling

### Filename to Enum Case Conversion

The script handles various filename patterns:

| Original Filename | Enum Case Name | Notes |
|------------------|----------------|-------|
| `facemask_circle.svg` | `FacemaskCircle` | Underscores → PascalCase |
| `home-2.svg` | `Home2` | Hyphens → PascalCase |
| `4_k.svg` | `Icon4K` | Numbers prefixed with "Icon" |
| `i_phone.svg` | `IPhone` | Special handling for "i" prefix |
| `add_circle.svg` | `AddCircle` | Standard conversion |

### Special Character Handling

- **Underscores and hyphens**: Converted to PascalCase boundaries
- **Numbers at start**: Prefixed with "Icon" to make valid PHP identifiers
- **Special characters**: Removed or converted to valid characters
- **Invalid names**: Fallback to MD5 hash with "Icon" prefix

## Integration with Package

### BladeUI Icons Integration

The generated enum values work seamlessly with BladeUI Icons:

```php
// In Blade templates
<x-icon :name="SolarIcon::FacemaskCircle->value" />

// With @svg directive
@svg(SolarIcon::FacemaskCircle->value)

// In PHP code
$iconName = SolarIcon::FacemaskCircle->value; // 'solar-bold-facemask_circle'
```

### Filament Integration

Perfect integration with Filament components:

```php
// In Filament resources
->icon(SolarIcon::Home->value)

// With different icon sets
->icon(SolarIcon::Home->forSet('solar-linear'))
```

## Maintenance

### When to Run the Script

Run the generator script when:

1. **New icons are added** to any Solar icon set
2. **Icons are renamed** or moved
3. **Icon sets are updated** with new versions
4. **Package is updated** with new Solar icon releases

### Automation

Consider adding the script to your deployment process:

```bash
# In your deployment script
php bin/generate-solar-enum.php
```

Or add it as a Composer script:

```json
{
    "scripts": {
        "generate-enum": "php bin/generate-solar-enum.php"
    }
}
```

Then run with:

```bash
composer generate-enum
```

## Error Handling

The script includes comprehensive error handling:

- **Missing directories**: Warns about missing icon set directories
- **File permissions**: Handles read/write permission issues
- **Invalid filenames**: Provides fallback naming for edge cases
- **Generation failures**: Clear error messages with exit codes

## Performance

The script is optimized for performance:

- **Efficient scanning**: Uses `RecursiveIteratorIterator` for fast directory traversal
- **Memory efficient**: Processes icons in batches
- **Caching friendly**: Only regenerates when needed
- **Fast execution**: Typically completes in under 5 seconds

## Conclusion

The Solar Icon Enum Generator ensures perfect consistency between your enum and actual SVG files, eliminating manual errors and providing a robust foundation for icon management in your Laravel/Filament applications.
