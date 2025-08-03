# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2025-08-03

### Added
- Initial release of Laravel Solar Icons package
- 6 different icon styles (Bold, Bold Duotone, Broken, Line Duotone, Linear, Outline)
- 1000+ high-quality SVG icons
- BladeUI Icons integration
- Type-safe enum system with IDE autocompletion
- Icon browser command for Laravel applications
- Comprehensive Laravel usage examples
- Laravel package auto-discovery support
- Helper methods for icon discovery and management

### Changed
- **BREAKING**: Converted from Filament-specific package to generic Laravel package
- **BREAKING**: Updated namespace from `Monsefeledrisse\FilamentSolarIcons` to `Monsefeledrisse\LaravelSolarIcons`
- **BREAKING**: Removed Filament-specific dependencies and code
- Updated package name from `monsefeledrisse/filament-solar-icons` to `monsefeledrisse/laravel-solar-icons`
- Rewritten documentation for Laravel usage
- Updated examples to focus on Laravel applications

### Removed
- Filament-specific service providers and compatibility helpers
- Filament v3/v4 specific features and integrations
- Filament-specific test files and examples
- Development artifacts and Filament-specific documentation

### Migration Guide
If you were using the Filament version of this package:
1. Update your composer.json to use `monsefeledrisse/laravel-solar-icons`
2. Update namespace imports from `Monsefeledrisse\FilamentSolarIcons` to `Monsefeledrisse\LaravelSolarIcons`
3. The SolarIcon enum cases remain the same, but usage patterns may differ
4. Review the new documentation for Laravel-specific usage examples
