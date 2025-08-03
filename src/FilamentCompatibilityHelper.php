<?php

declare(strict_types=1);

namespace Monsefeledrisse\FilamentSolarIcons;

/**
 * Filament Compatibility Helper
 * 
 * This class provides compatibility utilities for working with Filament
 * when it's available, without requiring it as a hard dependency.
 * 
 * @package Monsefeledrisse\FilamentSolarIcons
 */
class FilamentCompatibilityHelper
{
    /**
     * Check if Filament's ScalableIcon interface is available.
     */
    public static function isScalableIconAvailable(): bool
    {
        return interface_exists('Filament\Support\Contracts\ScalableIcon');
    }

    /**
     * Check if the SolarIcon enum implements ScalableIcon interface.
     */
    public static function implementsScalableIcon(): bool
    {
        if (!self::isScalableIconAvailable()) {
            return false;
        }

        return is_subclass_of(SolarIcon::class, 'Filament\Support\Contracts\ScalableIcon');
    }

    /**
     * Get icon for size with fallback behavior.
     * 
     * This method provides a consistent interface whether or not
     * Filament is available.
     */
    public static function getIconForSize(SolarIcon $icon, string $size): string
    {
        // SolarIcon always has the getIconForSize method
        return $icon->getIconForSize($size);
    }

    /**
     * Validate that an icon can be used with Filament components.
     */
    public static function validateFilamentCompatibility(SolarIcon $icon): bool
    {
        // Basic validation - icon has a value
        if (empty($icon->value)) {
            return false;
        }

        // If Filament is available, check interface compliance
        if (self::isScalableIconAvailable()) {
            return method_exists($icon, 'getIconForSize');
        }

        return true;
    }

    /**
     * Get compatibility information for debugging.
     */
    public static function getCompatibilityInfo(): array
    {
        return [
            'filament_available' => class_exists('Filament\FilamentServiceProvider'),
            'scalable_icon_interface_available' => self::isScalableIconAvailable(),
            'solar_icon_implements_interface' => self::implementsScalableIcon(),
            'solar_icon_has_method' => method_exists(SolarIcon::class, 'getIconForSize'),
        ];
    }
}
