# Version Management Guide

This document explains how to manage versions for the Solar Icons package.

## Current Version

The package is currently at version **1.0.0** and follows [Semantic Versioning](https://semver.org/).

## Version Management Tools

### 1. Command Line Tool

Use the built-in version management script:

```bash
# Show current version
php bin/version current

# Bump patch version (1.0.0 → 1.0.1)
php bin/version bump patch

# Bump minor version (1.0.0 → 1.1.0)  
php bin/version bump minor

# Bump major version (1.0.0 → 2.0.0)
php bin/version bump major

# Set specific version
php bin/version set 1.2.3

# Show help
php bin/version help
```

### 2. Composer Scripts

Use convenient composer scripts:

```bash
# Show current version
composer version:current

# Bump versions
composer version:patch   # 1.0.0 → 1.0.1
composer version:minor   # 1.0.0 → 1.1.0  
composer version:major   # 1.0.0 → 2.0.0

# Access full version tool
composer version help
```

## Semantic Versioning Guidelines

This package follows [Semantic Versioning](https://semver.org/) with the format `MAJOR.MINOR.PATCH`:

### MAJOR Version (Breaking Changes)
Increment when you make incompatible API changes:
- Removing public methods or properties
- Changing method signatures
- Removing enum cases
- Changing configuration structure
- Dropping PHP or Laravel version support

**Example**: `1.5.2 → 2.0.0`

### MINOR Version (New Features)
Increment when you add functionality in a backwards compatible manner:
- Adding new icon styles
- Adding new helper methods
- Adding new configuration options
- Adding new command options
- Improving performance without breaking changes

**Example**: `1.5.2 → 1.6.0`

### PATCH Version (Bug Fixes)
Increment when you make backwards compatible bug fixes:
- Fixing icon rendering issues
- Correcting path resolution
- Fixing documentation
- Performance improvements
- Security patches

**Example**: `1.5.2 → 1.5.3`

## Release Process

### 1. Prepare Release

```bash
# Run tests to ensure everything works
composer test

# Update version (choose appropriate bump type)
composer version:patch  # or minor/major

# Update CHANGELOG.md with new changes
# Edit the file to document what changed
```

### 2. Commit and Tag

```bash
# Commit version changes
git add composer.json CHANGELOG.md
git commit -m "Release version 1.0.1"

# Create and push tag
git tag v1.0.1
git push origin main --tags
```

### 3. Create GitHub Release

1. Go to GitHub repository
2. Click "Releases" → "Create a new release"
3. Select the tag you just created
4. Copy changelog content for release notes
5. Publish the release

## Version History

All version changes are documented in [CHANGELOG.md](CHANGELOG.md).

## Checking for Updates

### For Package Users

Users can check for updates using composer:

```bash
# Check for available updates
composer outdated monsefeledrisse/filament-solar-icons

# Update to latest version
composer update monsefeledrisse/filament-solar-icons

# Update to specific version
composer require monsefeledrisse/filament-solar-icons:^1.1.0
```

### Version Constraints

Recommended version constraints in `composer.json`:

```json
{
    "require": {
        "monsefeledrisse/filament-solar-icons": "^1.0"
    }
}
```

This allows:
- ✅ `1.0.0` to `1.9.9` (compatible updates)
- ❌ `2.0.0` and above (breaking changes)

## Automated Version Management

### GitHub Actions (Optional)

You can set up automated releases with GitHub Actions:

```yaml
# .github/workflows/release.yml
name: Release
on:
  push:
    tags: ['v*']
jobs:
  release:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Create Release
        uses: actions/create-release@v1
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
        with:
          tag_name: ${{ github.ref }}
          release_name: Release ${{ github.ref }}
```

## Best Practices

1. **Always run tests** before releasing
2. **Update CHANGELOG.md** with every release
3. **Use semantic versioning** consistently
4. **Tag releases** in git for easy tracking
5. **Document breaking changes** clearly
6. **Test upgrades** in development environments first

## Troubleshooting

### Version Script Issues

If the version script doesn't work:

```bash
# Make sure it's executable
chmod +x bin/version

# Check PHP version (requires PHP 8.1+)
php --version

# Run with full path
php /full/path/to/bin/version current
```

### Composer Issues

If composer scripts don't work:

```bash
# Clear composer cache
composer clear-cache

# Reinstall dependencies
composer install

# Check composer version
composer --version
```
