<?php

declare(strict_types=1);

namespace Monsefeledrisse\FilamentSolarIcons;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Solar Icon Helper
 *
 * Utility class for managing Solar icons, generating mappings,
 * and providing helper methods for icon discovery and usage.
 *
 * @package Monsefeledrisse\FilamentSolarIcons
 */
class SolarIconHelper
{
    /**
     * Available Solar icon styles
     */
    private const AVAILABLE_STYLES = [
        'solar-bold',
        'solar-bold-duotone',
        'solar-broken',
        'solar-line-duotone',
        'solar-linear',
        'solar-outline'
    ];

    /**
     * Cache key for icon files
     */
    private const CACHE_KEY = 'solar_icons_files';

    /**
     * Cache TTL in seconds (1 hour)
     */
    private const CACHE_TTL = 3600;
    /**
     * Get all available icon files from the resources directory.
     *
     * @return Collection<int, array{key: string, name: string, style: string, path: string, normalized_name: string}>
     */
    public static function getAllIconFiles(): Collection
    {
        // Use cache if enabled and Laravel is available
        if (self::isCacheEnabled()) {
            try {
                return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
                    return self::loadIconFiles();
                });
            } catch (\Throwable $e) {
                // Fall back to direct loading if cache fails
                return self::loadIconFiles();
            }
        }

        return self::loadIconFiles();
    }

    /**
     * Check if caching is enabled and available.
     */
    private static function isCacheEnabled(): bool
    {
        try {
            return function_exists('config') && config('solar-icons.cache_icons', true);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Load icon files from the filesystem.
     *
     * @return Collection<int, array{key: string, name: string, style: string, path: string, normalized_name: string}>
     */
    private static function loadIconFiles(): Collection
    {
        $iconPath = self::getIconBasePath();
        $icons = collect();

        if (!self::validateIconBasePath($iconPath)) {
            return $icons;
        }

        foreach (self::AVAILABLE_STYLES as $style) {
            $stylePath = $iconPath . '/' . $style;

            if (!is_dir($stylePath)) {
                self::logMissingStyleDirectory($style, $stylePath);
                continue;
            }

            try {
                $styleIcons = self::loadIconsFromStyleDirectory($stylePath, $style);
                $icons = $icons->merge($styleIcons);
            } catch (\Throwable $e) {
                self::logStyleLoadError($style, $e);
            }
        }

        return $icons;
    }

    /**
     * Get the base path for icons.
     */
    private static function getIconBasePath(): string
    {
        return __DIR__ . '/../resources/icons/solar';
    }

    /**
     * Validate that the icon base path exists and is readable.
     */
    private static function validateIconBasePath(string $iconPath): bool
    {
        if (!is_dir($iconPath)) {
            self::safeLog('error', "Solar Icons: Base icon directory does not exist: {$iconPath}");
            return false;
        }

        if (!is_readable($iconPath)) {
            self::safeLog('error', "Solar Icons: Base icon directory is not readable: {$iconPath}");
            return false;
        }

        return true;
    }

    /**
     * Safely log messages when Laravel is available.
     */
    private static function safeLog(string $level, string $message): void
    {
        try {
            if (class_exists('Illuminate\Support\Facades\Log')) {
                Log::{$level}($message);
            }
        } catch (\Throwable $e) {
            // Silently ignore logging errors when not in Laravel context
        }
    }

    /**
     * Load icons from a specific style directory.
     *
     * @return Collection<int, array{key: string, name: string, style: string, path: string, normalized_name: string}>
     */
    private static function loadIconsFromStyleDirectory(string $stylePath, string $style): Collection
    {
        $icons = collect();
        $files = self::getAllFilesInDirectory($stylePath);

        foreach ($files as $filePath) {
            if (!str_ends_with(strtolower($filePath), '.svg')) {
                continue;
            }

            try {
                $iconData = self::createIconDataFromPath($filePath, $style);
                $icons->push($iconData);
            } catch (\Throwable $e) {
                self::safeLog('warning', "Solar Icons: Failed to process icon file {$filePath}: {$e->getMessage()}");
            }
        }

        return $icons;
    }

    /**
     * Get all files in a directory recursively.
     *
     * @return array<string> Array of file paths
     */
    private static function getAllFilesInDirectory(string $directory): array
    {
        // Try Laravel's File facade first if available
        try {
            if (class_exists('Illuminate\Support\Facades\File')) {
                $files = File::allFiles($directory);
                return array_map(fn($file) => $file->getPathname(), $files);
            }
        } catch (\Throwable $e) {
            // Fall back to native PHP
        }

        // Use native PHP recursive directory iterator
        $files = [];
        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $files[] = $file->getPathname();
                }
            }
        } catch (\Throwable $e) {
            self::safeLog('error', "Solar Icons: Failed to read directory {$directory}: {$e->getMessage()}");
        }

        return $files;
    }

    /**
     * Create icon data array from file path.
     *
     * @return array{key: string, name: string, style: string, path: string, normalized_name: string}
     */
    private static function createIconDataFromPath(string $filePath, string $style): array
    {
        $iconName = pathinfo($filePath, PATHINFO_FILENAME);
        $normalizedName = self::normalizeIconName($iconName);

        return [
            'key' => $style . '-' . $normalizedName,
            'name' => $iconName,
            'style' => $style,
            'path' => $filePath,
            'normalized_name' => $normalizedName,
        ];
    }

    /**
     * Normalize icon name for use in enum cases.
     *
     * @param string $name The raw icon name
     * @return string The normalized name suitable for enum cases
     */
    public static function normalizeIconName(string $name): string
    {
        // Remove file extension if present
        $name = pathinfo($name, PATHINFO_FILENAME);

        // Convert to PascalCase for enum cases
        $name = str_replace([' ', '-', '_', '.', ','], ' ', $name);
        $name = ucwords($name);
        $name = str_replace(' ', '', $name);

        // Handle numbers at the start (PHP enum cases cannot start with numbers)
        if (is_numeric(substr($name, 0, 1))) {
            $name = 'Icon' . $name;
        }

        // Ensure the name is not empty
        if (empty($name)) {
            $name = 'UnknownIcon';
        }

        return $name;
    }

    /**
     * Log missing style directory warning.
     */
    private static function logMissingStyleDirectory(string $style, string $path): void
    {
        try {
            if (function_exists('config') && config('solar-icons.development.log_missing_icons', false)) {
                self::safeLog('warning', "Solar Icons: Style directory missing for {$style}: {$path}");
            }
        } catch (\Throwable $e) {
            // Silently ignore logging errors when not in Laravel context
        }
    }

    /**
     * Log style loading error.
     */
    private static function logStyleLoadError(string $style, \Throwable $e): void
    {
        self::safeLog('error', "Solar Icons: Failed to load icons for style {$style}: {$e->getMessage()}");
    }

    /**
     * Clear the icon cache.
     */
    public static function clearCache(): bool
    {
        try {
            if (class_exists('Illuminate\Support\Facades\Cache')) {
                return Cache::forget(self::CACHE_KEY);
            }
        } catch (\Throwable $e) {
            // Cache not available
        }
        return false;
    }

    /**
     * Generate enum cases for all available icons.
     *
     * @return string PHP enum cases as a string
     */
    public static function generateEnumCases(): string
    {
        try {
            $icons = self::getAllIconFiles();
            $cases = [];
            $usedNames = [];

            foreach ($icons as $icon) {
                $enumName = self::normalizeIconName($icon['name']);

                // Handle duplicates by adding style prefix
                if (in_array($enumName, $usedNames, true)) {
                    $stylePrefix = self::getStylePrefix($icon['style']);
                    $enumName = $stylePrefix . $enumName;
                }

                // Ensure uniqueness even after prefix addition
                $originalEnumName = $enumName;
                $counter = 1;
                while (in_array($enumName, $usedNames, true)) {
                    $enumName = $originalEnumName . $counter;
                    $counter++;
                }

                $usedNames[] = $enumName;
                $cases[] = "    case {$enumName} = '{$icon['key']}';";
            }

            return implode("\n", $cases);
        } catch (\Throwable $e) {
            self::safeLog('error', "Solar Icons: Failed to generate enum cases: {$e->getMessage()}");
            return "    // Error generating enum cases: {$e->getMessage()}";
        }
    }

    /**
     * Get style prefix for enum case generation.
     */
    private static function getStylePrefix(string $style): string
    {
        $prefix = str_replace(['solar-', '-'], ['', ''], $style);
        return ucfirst($prefix);
    }

    /**
     * Get popular/commonly used icons for quick reference
     */
    public static function getPopularIcons(): array
    {
        return [
            // Navigation & UI
            'home' => 'Home page or dashboard',
            'user' => 'User profile or account',
            'users' => 'Multiple users or team',
            'settings' => 'Settings or configuration',
            'search' => 'Search functionality',
            'bell' => 'Notifications',
            'calendar' => 'Calendar or dates',
            'clock-circle' => 'Time or schedule',
            
            // Actions
            'download' => 'Download files',
            'upload' => 'Upload files',
            'pen' => 'Edit or modify',
            'trash-bin-minimalistic' => 'Delete or remove',
            'add-circle' => 'Add new item',
            'close-circle' => 'Close or cancel',
            'check-circle' => 'Success or complete',
            
            // Content
            'star' => 'Favorite or rating',
            'heart' => 'Like or favorite',
            'eye' => 'View or visibility',
            'eye-closed' => 'Hide or invisible',
            'lock' => 'Secure or private',
            'lock-unlocked' => 'Unlocked or public',
            
            // Communication
            'letter' => 'Email or message',
            'phone' => 'Phone or contact',
            'chat-round-dots' => 'Chat or conversation',
            'chat-round-line' => 'Messages',
            
            // Media
            'gallery' => 'Images or media',
            'file-text' => 'Documents or files',
            'folder' => 'Folders or directories',
            'camera' => 'Photos or camera',
            'videocamera' => 'Video recording',
            
            // Status
            'info-circle' => 'Information',
            'danger-triangle' => 'Warning or alert',
            'question-circle' => 'Help or questions',
            
            // Navigation arrows
            'arrow-up' => 'Move up',
            'arrow-down' => 'Move down',
            'arrow-left' => 'Go back',
            'arrow-right' => 'Go forward',
            'alt-arrow-up' => 'Chevron up',
            'alt-arrow-down' => 'Chevron down',
            'alt-arrow-left' => 'Chevron left',
            'alt-arrow-right' => 'Chevron right',
        ];
    }

    /**
     * Get icon recommendations based on context
     */
    public static function getIconRecommendations(string $context): array
    {
        $recommendations = [
            'navigation' => [
                SolarIcon::Home,
                SolarIcon::User,
                SolarIcon::Users,
                SolarIcon::Settings,
                SolarIcon::Bell,
                SolarIcon::Calendar,
            ],
            'actions' => [
                SolarIcon::Add,
                SolarIcon::Edit,
                SolarIcon::Delete,
                SolarIcon::Save,
                SolarIcon::Download,
                SolarIcon::Upload,
                SolarIcon::Copy,
                SolarIcon::Share,
            ],
            'status' => [
                SolarIcon::Success,
                SolarIcon::Warning,
                SolarIcon::Error,
                SolarIcon::Info,
                SolarIcon::Question,
            ],
            'media' => [
                SolarIcon::Image,
                SolarIcon::File,
                SolarIcon::Folder,
                SolarIcon::Camera,
                SolarIcon::Video,
                SolarIcon::Music,
            ],
            'communication' => [
                SolarIcon::Mail,
                SolarIcon::Phone,
                SolarIcon::Chat,
                SolarIcon::Message,
                SolarIcon::Notification,
            ],
            'business' => [
                SolarIcon::Money,
                SolarIcon::Cart,
                SolarIcon::Shop,
                SolarIcon::Tag,
                SolarIcon::Receipt,
                SolarIcon::Chart,
                SolarIcon::Analytics,
            ],
        ];

        return $recommendations[$context] ?? [];
    }

    /**
     * Search icons by name or description.
     *
     * @param string $query Search query
     * @return Collection<int, array{key: string, name: string, style: string, path: string, normalized_name: string}>
     */
    public static function searchIcons(string $query): Collection
    {
        if (empty(trim($query))) {
            return collect();
        }

        try {
            $icons = self::getAllIconFiles();
            $query = strtolower(trim($query));

            return $icons->filter(function ($icon) use ($query) {
                return str_contains(strtolower($icon['name']), $query) ||
                       str_contains(strtolower($icon['normalized_name']), $query) ||
                       str_contains(strtolower($icon['key']), $query);
            });
        } catch (\Throwable $e) {
            self::safeLog('error', "Solar Icons: Search failed for query '{$query}': {$e->getMessage()}");
            return collect();
        }
    }

    /**
     * Get icon usage statistics (if tracking is implemented)
     */
    public static function getIconUsageStats(): array
    {
        // This could be implemented to track which icons are used most frequently
        // in your application for better recommendations
        return [
            'most_used' => [
                'solar-linear-home',
                'solar-linear-user',
                'solar-linear-settings',
                'solar-outline-edit',
                'solar-bold-check-circle',
            ],
            'least_used' => [],
            'total_icons' => self::getAllIconFiles()->count(),
        ];
    }

    /**
     * Validate that an icon exists in the file system.
     *
     * @param string $iconName The icon key to check
     * @return bool True if the icon exists, false otherwise
     */
    public static function iconExists(string $iconName): bool
    {
        if (empty(trim($iconName))) {
            return false;
        }

        try {
            $icons = self::getAllIconFiles();
            return $icons->contains('key', $iconName);
        } catch (\Throwable $e) {
            self::safeLog('error', "Solar Icons: Failed to check icon existence for '{$iconName}': {$e->getMessage()}");
            return false;
        }
    }

    /**
     * Get the file path for a specific icon.
     *
     * @param string $iconName The icon key
     * @return string|null The file path or null if not found
     */
    public static function getIconPath(string $iconName): ?string
    {
        if (empty(trim($iconName))) {
            return null;
        }

        try {
            $icons = self::getAllIconFiles();
            $icon = $icons->firstWhere('key', $iconName);

            return $icon ? $icon['path'] : null;
        } catch (\Throwable $e) {
            self::safeLog('error', "Solar Icons: Failed to get icon path for '{$iconName}': {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Get all icons for a specific style.
     *
     * @param string $style The style name (without 'solar-' prefix)
     * @return Collection<int, array{key: string, name: string, style: string, path: string, normalized_name: string}>
     */
    public static function getIconsByStyle(string $style): Collection
    {
        if (empty(trim($style))) {
            return collect();
        }

        try {
            $fullStyleName = str_starts_with($style, 'solar-') ? $style : "solar-{$style}";

            if (!in_array($fullStyleName, self::AVAILABLE_STYLES, true)) {
                self::safeLog('warning', "Solar Icons: Unknown style requested: {$style}");
                return collect();
            }

            $icons = self::getAllIconFiles();
            return $icons->where('style', $fullStyleName);
        } catch (\Throwable $e) {
            self::safeLog('error', "Solar Icons: Failed to get icons by style '{$style}': {$e->getMessage()}");
            return collect();
        }
    }

    /**
     * Convert an icon name between styles.
     *
     * @param string $iconName The current icon key
     * @param string $newStyle The target style (without 'solar-' prefix)
     * @return string|null The new icon key or null if conversion fails
     */
    public static function convertIconStyle(string $iconName, string $newStyle): ?string
    {
        if (empty(trim($iconName)) || empty(trim($newStyle))) {
            return null;
        }

        try {
            // Extract the base name from the current icon
            $parts = explode('-', $iconName, 3);
            if (count($parts) < 3) {
                self::safeLog('warning', "Solar Icons: Invalid icon name format for conversion: {$iconName}");
                return null;
            }

            $baseName = $parts[2];
            $fullNewStyle = str_starts_with($newStyle, 'solar-') ? $newStyle : "solar-{$newStyle}";
            $newIconName = "{$fullNewStyle}-{$baseName}";

            // Check if the new icon exists
            return self::iconExists($newIconName) ? $newIconName : null;
        } catch (\Throwable $e) {
            self::safeLog('error', "Solar Icons: Failed to convert icon style from '{$iconName}' to '{$newStyle}': {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Get available icon styles.
     *
     * @return array<string> Array of available style names
     */
    public static function getAvailableStyles(): array
    {
        return self::AVAILABLE_STYLES;
    }
}
