<?php

namespace Monsefeledrisse\FilamentSolarIcons;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

/**
 * Solar Icon Helper
 * 
 * Utility class for managing Solar icons, generating mappings,
 * and providing helper methods for icon discovery and usage.
 */
class SolarIconHelper
{
    /**
     * Get all available icon files from the resources directory
     */
    public static function getAllIconFiles(): Collection
    {
        $iconPath = __DIR__ . '/../resources/icons/solar';
        $icons = collect();

        if (!is_dir($iconPath)) {
            return $icons;
        }

        $styles = ['solar-bold', 'solar-bold-duotone', 'solar-broken', 'solar-line-duotone', 'solar-linear', 'solar-outline'];

        foreach ($styles as $style) {
            $stylePath = $iconPath . '/' . $style;
            if (is_dir($stylePath)) {
                $files = File::allFiles($stylePath);
                foreach ($files as $file) {
                    if ($file->getExtension() === 'svg') {
                        $iconName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                        $iconKey = $style . '-' . $this->normalizeIconName($iconName);
                        
                        $icons->push([
                            'key' => $iconKey,
                            'name' => $iconName,
                            'style' => $style,
                            'path' => $file->getPathname(),
                            'normalized_name' => $this->normalizeIconName($iconName),
                        ]);
                    }
                }
            }
        }

        return $icons;
    }

    /**
     * Normalize icon name for use in enum cases
     */
    public static function normalizeIconName(string $name): string
    {
        // Remove file extension
        $name = pathinfo($name, PATHINFO_FILENAME);
        
        // Convert to PascalCase for enum cases
        $name = str_replace([' ', '-', '_', '.', ','], ' ', $name);
        $name = ucwords($name);
        $name = str_replace(' ', '', $name);
        
        // Handle numbers at the start
        if (is_numeric(substr($name, 0, 1))) {
            $name = 'Icon' . $name;
        }
        
        return $name;
    }

    /**
     * Generate enum cases for all available icons
     */
    public static function generateEnumCases(): string
    {
        $icons = self::getAllIconFiles();
        $cases = [];
        $usedNames = [];

        foreach ($icons as $icon) {
            $enumName = self::normalizeIconName($icon['name']);
            
            // Handle duplicates by adding style prefix
            if (in_array($enumName, $usedNames)) {
                $stylePrefix = ucfirst(str_replace(['solar-', '-'], ['', ''], $icon['style']));
                $enumName = $stylePrefix . $enumName;
            }
            
            $usedNames[] = $enumName;
            $cases[] = "    case {$enumName} = '{$icon['key']}';";
        }

        return implode("\n", $cases);
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
     * Search icons by name or description
     */
    public static function searchIcons(string $query): Collection
    {
        $icons = self::getAllIconFiles();
        $query = strtolower($query);

        return $icons->filter(function ($icon) use ($query) {
            return str_contains(strtolower($icon['name']), $query) ||
                   str_contains(strtolower($icon['normalized_name']), $query);
        });
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
     * Validate icon exists in the file system
     */
    public static function iconExists(string $iconName): bool
    {
        $icons = self::getAllIconFiles();
        return $icons->contains('key', $iconName);
    }

    /**
     * Get icon file path
     */
    public static function getIconPath(string $iconName): ?string
    {
        $icons = self::getAllIconFiles();
        $icon = $icons->firstWhere('key', $iconName);
        
        return $icon ? $icon['path'] : null;
    }

    /**
     * Get all icons for a specific style
     */
    public static function getIconsByStyle(string $style): Collection
    {
        $icons = self::getAllIconFiles();
        return $icons->where('style', "solar-{$style}");
    }

    /**
     * Convert icon name between styles
     */
    public static function convertIconStyle(string $iconName, string $newStyle): ?string
    {
        // Extract the base name from the current icon
        $parts = explode('-', $iconName, 3);
        if (count($parts) < 3) {
            return null;
        }

        $baseName = $parts[2];
        $newIconName = "solar-{$newStyle}-{$baseName}";

        // Check if the new icon exists
        return self::iconExists($newIconName) ? $newIconName : null;
    }
}
