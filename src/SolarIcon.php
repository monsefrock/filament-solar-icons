<?php

declare(strict_types=1);

namespace Monsefeledrisse\FilamentSolarIcons;

use Filament\Support\Contracts\ScalableIcon;

/**
 * Solar Icon Set for Filament v4
 *
 * This enum provides type-safe access to Solar icons in Filament v4,
 * similar to how Heroicon works in the core Filament package.
 *
 * Each enum case represents a specific Solar icon with its full identifier.
 * The enum implements ScalableIcon to work seamlessly with Filament components.
 *
 * @example
 * ```php
 * // In Filament components
 * Action::make('star')->icon(SolarIcon::Star)
 * TextInput::make('name')->prefixIcon(SolarIcon::OutlineUser)
 * NavigationItem::make('Dashboard')->icon(SolarIcon::LinearHome)
 * ```
 *
 * @package Monsefeledrisse\FilamentSolarIcons
 */
enum SolarIcon: string implements ScalableIcon
{
    // Common UI Icons - Bold Style
    case Home = 'solar-bold-home';
    case User = 'solar-bold-user';
    case Users = 'solar-bold-users';
    case Settings = 'solar-bold-settings';
    case Search = 'solar-bold-magnifer';
    case Bell = 'solar-bold-bell';
    case Calendar = 'solar-bold-calendar';
    case Clock = 'solar-bold-clock-circle';
    case Download = 'solar-bold-download';
    case Upload = 'solar-bold-upload';
    case Edit = 'solar-bold-pen';
    case Delete = 'solar-bold-trash-bin-minimalistic';
    case Add = 'solar-bold-add-circle';
    case Remove = 'solar-bold-close-circle';
    case Check = 'solar-bold-check-circle';
    case Star = 'solar-bold-star';
    case Heart = 'solar-bold-heart';
    case Eye = 'solar-bold-eye';
    case EyeSlash = 'solar-bold-eye-closed';
    case Lock = 'solar-bold-lock';
    case Unlock = 'solar-bold-lock-unlocked';
    case Mail = 'solar-bold-letter';
    case Phone = 'solar-bold-phone';
    case Map = 'solar-bold-map-point';
    case Filter = 'solar-bold-filter';
    case Sort = 'solar-bold-sort-vertical';
    case Menu = 'solar-bold-hamburger-menu';
    case Grid = 'solar-bold-widget-4';
    case List = 'solar-bold-list';
    case Card = 'solar-bold-card';
    case Image = 'solar-bold-gallery';
    case File = 'solar-bold-file-text';
    case Folder = 'solar-bold-folder';
    case Archive = 'solar-bold-archive-minimalistic';
    case Share = 'solar-bold-share';
    case Copy = 'solar-bold-copy';
    case Paste = 'solar-bold-clipboard-text';
    case Cut = 'solar-bold-scissors';
    case Undo = 'solar-bold-undo-left';
    case Redo = 'solar-bold-redo-right';
    case Save = 'solar-bold-diskette';
    case Print = 'solar-bold-printer';
    case Refresh = 'solar-bold-refresh';
    case Sync = 'solar-bold-refresh-circle';
    case Power = 'solar-bold-power';
    case Logout = 'solar-bold-logout-2';
    case Login = 'solar-bold-login-2';
    
    // Navigation Icons - Bold Style
    case ArrowUp = 'solar-bold-arrow-up';
    case ArrowDown = 'solar-bold-arrow-down';
    case ArrowLeft = 'solar-bold-arrow-left';
    case ArrowRight = 'solar-bold-arrow-right';
    case ChevronUp = 'solar-bold-alt-arrow-up';
    case ChevronDown = 'solar-bold-alt-arrow-down';
    case ChevronLeft = 'solar-bold-alt-arrow-left';
    case ChevronRight = 'solar-bold-alt-arrow-right';
    case DoubleArrowUp = 'solar-bold-double-alt-arrow-up';
    case DoubleArrowDown = 'solar-bold-double-alt-arrow-down';
    case DoubleArrowLeft = 'solar-bold-double-alt-arrow-left';
    case DoubleArrowRight = 'solar-bold-double-alt-arrow-right';
    
    // Status Icons - Bold Style
    case Success = 'solar-bold-check-circle';
    case Warning = 'solar-bold-danger-triangle';
    case Error = 'solar-bold-close-circle';
    case Info = 'solar-bold-info-circle';
    case Question = 'solar-bold-question-circle';
    
    // Business Icons - Bold Style
    case Money = 'solar-bold-dollar-minimalistic';
    case Cart = 'solar-bold-cart-large-2';
    case Shop = 'solar-bold-shop';
    case Tag = 'solar-bold-tag-price';
    case Receipt = 'solar-bold-bill-list';
    case CreditCard = 'solar-bold-card';
    case Wallet = 'solar-bold-wallet-money';
    case Chart = 'solar-bold-chart';
    case Analytics = 'solar-bold-graph-up';
    case Report = 'solar-bold-document-text';
    
    // Communication Icons - Bold Style
    case Chat = 'solar-bold-chat-round-dots';
    case Message = 'solar-bold-chat-round-line';
    case Comment = 'solar-bold-chat-square-like';
    case Notification = 'solar-bold-bell-bing';
    case Announcement = 'solar-bold-soundwave-circle';
    
    // Media Icons - Bold Style
    case Play = 'solar-bold-play';
    case Pause = 'solar-bold-pause';
    case Stop = 'solar-bold-stop';
    case Record = 'solar-bold-record-circle';
    case Camera = 'solar-bold-camera';
    case Video = 'solar-bold-videocamera';
    case Music = 'solar-bold-music-note';
    case Volume = 'solar-bold-volume-loud';
    case VolumeOff = 'solar-bold-volume-cross';
    
    // Device Icons - Bold Style
    case Desktop = 'solar-bold-monitor';
    case Laptop = 'solar-bold-laptop';
    case Tablet = 'solar-bold-tablet';
    case Mobile = 'solar-bold-smartphone';
    case Watch = 'solar-bold-smartwatch-square';
    case Headphones = 'solar-bold-headphones';
    case Keyboard = 'solar-bold-keyboard';
    case Mouse = 'solar-bold-mouse';
    
    // Weather Icons - Bold Style
    case Sun = 'solar-bold-sun';
    case Moon = 'solar-bold-moon';
    case Cloud = 'solar-bold-cloud';
    case Rain = 'solar-bold-cloud-rain';
    case Snow = 'solar-bold-cloud-snow';
    case Storm = 'solar-bold-cloud-storm';
    case Wind = 'solar-bold-wind';
    
    // Transport Icons - Bold Style
    case Car = 'solar-bold-car';
    case Bus = 'solar-bold-bus';
    case Train = 'solar-bold-train';
    case Plane = 'solar-bold-plane';
    case Ship = 'solar-bold-ship';
    case Bike = 'solar-bold-bicycle';
    case Walk = 'solar-bold-walking';
    
    // Outline Variants (Most commonly used)
    case OutlineHome = 'solar-outline-home';
    case OutlineUser = 'solar-outline-user';
    case OutlineUsers = 'solar-outline-users';
    case OutlineSettings = 'solar-outline-settings';
    case OutlineSearch = 'solar-outline-magnifer';
    case OutlineBell = 'solar-outline-bell';
    case OutlineCalendar = 'solar-outline-calendar';
    case OutlineClock = 'solar-outline-clock-circle';
    case OutlineDownload = 'solar-outline-download';
    case OutlineUpload = 'solar-outline-upload';
    case OutlineEdit = 'solar-outline-pen';
    case OutlineDelete = 'solar-outline-trash-bin-minimalistic';
    case OutlineAdd = 'solar-outline-add-circle';
    case OutlineRemove = 'solar-outline-close-circle';
    case OutlineCheck = 'solar-outline-check-circle';
    case OutlineStar = 'solar-outline-star';
    case OutlineHeart = 'solar-outline-heart';
    case OutlineEye = 'solar-outline-eye';
    case OutlineEyeSlash = 'solar-outline-eye-closed';
    case OutlineLock = 'solar-outline-lock';
    case OutlineUnlock = 'solar-outline-lock-unlocked';
    case OutlineMail = 'solar-outline-letter';
    case OutlinePhone = 'solar-outline-phone';
    case OutlineMap = 'solar-outline-map-point';
    case OutlineFilter = 'solar-outline-filter';
    case OutlineSort = 'solar-outline-sort-vertical';
    case OutlineMenu = 'solar-outline-hamburger-menu';
    case OutlineGrid = 'solar-outline-widget-4';
    case OutlineList = 'solar-outline-list';
    case OutlineCard = 'solar-outline-card';
    case OutlineImage = 'solar-outline-gallery';
    case OutlineFile = 'solar-outline-file-text';
    case OutlineFolder = 'solar-outline-folder';
    case OutlineArchive = 'solar-outline-archive-minimalistic';
    case OutlineShare = 'solar-outline-share';
    case OutlineCopy = 'solar-outline-copy';
    case OutlinePaste = 'solar-outline-clipboard-text';
    case OutlineCut = 'solar-outline-scissors';
    case OutlineUndo = 'solar-outline-undo-left';
    case OutlineRedo = 'solar-outline-redo-right';
    case OutlineSave = 'solar-outline-diskette';
    case OutlinePrint = 'solar-outline-printer';
    case OutlineRefresh = 'solar-outline-refresh';
    case OutlineSync = 'solar-outline-refresh-circle';
    case OutlinePower = 'solar-outline-power';
    case OutlineLogout = 'solar-outline-logout-2';
    case OutlineLogin = 'solar-outline-login-2';
    
    // Navigation Icons - Outline Style
    case OutlineArrowUp = 'solar-outline-arrow-up';
    case OutlineArrowDown = 'solar-outline-arrow-down';
    case OutlineArrowLeft = 'solar-outline-arrow-left';
    case OutlineArrowRight = 'solar-outline-arrow-right';
    case OutlineChevronUp = 'solar-outline-alt-arrow-up';
    case OutlineChevronDown = 'solar-outline-alt-arrow-down';
    case OutlineChevronLeft = 'solar-outline-alt-arrow-left';
    case OutlineChevronRight = 'solar-outline-alt-arrow-right';
    
    // Status Icons - Outline Style
    case OutlineSuccess = 'solar-outline-check-circle';
    case OutlineWarning = 'solar-outline-danger-triangle';
    case OutlineError = 'solar-outline-close-circle';
    case OutlineInfo = 'solar-outline-info-circle';
    case OutlineQuestion = 'solar-outline-question-circle';
    
    // Linear Variants (Clean, minimal style)
    case LinearHome = 'solar-linear-home';
    case LinearUser = 'solar-linear-user';
    case LinearUsers = 'solar-linear-users';
    case LinearSettings = 'solar-linear-settings';
    case LinearSearch = 'solar-linear-magnifer';
    case LinearBell = 'solar-linear-bell';
    case LinearCalendar = 'solar-linear-calendar';
    case LinearClock = 'solar-linear-clock-circle';
    case LinearDownload = 'solar-linear-download';
    case LinearUpload = 'solar-linear-upload';
    case LinearEdit = 'solar-linear-pen';
    case LinearDelete = 'solar-linear-trash-bin-minimalistic';
    case LinearAdd = 'solar-linear-add-circle';
    case LinearRemove = 'solar-linear-close-circle';
    case LinearCheck = 'solar-linear-check-circle';
    case LinearStar = 'solar-linear-star';
    case LinearHeart = 'solar-linear-heart';
    case LinearEye = 'solar-linear-eye';
    case LinearEyeSlash = 'solar-linear-eye-closed';
    case LinearLock = 'solar-linear-lock';
    case LinearUnlock = 'solar-linear-lock-unlocked';
    case LinearMail = 'solar-linear-letter';
    case LinearPhone = 'solar-linear-phone';
    case LinearMap = 'solar-linear-map-point';
    case LinearFilter = 'solar-linear-filter';
    case LinearSort = 'solar-linear-sort-vertical';
    case LinearMenu = 'solar-linear-hamburger-menu';
    case LinearGrid = 'solar-linear-widget-4';
    case LinearList = 'solar-linear-list';
    case LinearCard = 'solar-linear-card';
    case LinearImage = 'solar-linear-gallery';
    case LinearFile = 'solar-linear-file-text';
    case LinearFolder = 'solar-linear-folder';
    case LinearArchive = 'solar-linear-archive-minimalistic';
    case LinearShare = 'solar-linear-share';
    case LinearCopy = 'solar-linear-copy';
    case LinearPaste = 'solar-linear-clipboard-text';
    case LinearCut = 'solar-linear-scissors';
    case LinearUndo = 'solar-linear-undo-left';
    case LinearRedo = 'solar-linear-redo-right';
    case LinearSave = 'solar-linear-diskette';
    case LinearPrint = 'solar-linear-printer';
    case LinearRefresh = 'solar-linear-refresh';
    case LinearSync = 'solar-linear-refresh-circle';
    case LinearPower = 'solar-linear-power';
    case LinearLogout = 'solar-linear-logout-2';
    case LinearLogin = 'solar-linear-login-2';

    public function getIconName(): string
    {
        return $this->value;
    }

    /**
     * Get all available Solar icon styles with descriptions.
     *
     * @return array<string, string> Array of style names and descriptions
     */
    public static function getAvailableStyles(): array
    {
        return [
            'bold' => 'Bold - Filled, strong visual weight',
            'bold-duotone' => 'Bold Duotone - Two-tone bold style',
            'broken' => 'Broken - Stylized broken line style',
            'line-duotone' => 'Line Duotone - Two-tone line style',
            'linear' => 'Linear - Clean, minimal lines',
            'outline' => 'Outline - Clean outlined style',
        ];
    }

    /**
     * Get icon identifier by name and style.
     *
     * @param string $name The icon name
     * @param string $style The icon style (default: 'linear')
     * @return string The full icon identifier
     */
    public static function getIcon(string $name, string $style = 'linear'): string
    {
        return "solar-{$style}-{$name}";
    }

    /**
     * Check if an icon exists in the enum.
     *
     * @param string $iconName The icon identifier to check
     * @return bool True if the icon exists in the enum, false otherwise
     */
    public static function exists(string $iconName): bool
    {
        if (empty(trim($iconName))) {
            return false;
        }

        return collect(self::cases())->contains(fn($case) => $case->value === $iconName);
    }



    /**
     * Get all enum cases as an array of values.
     *
     * @return array<string> Array of all icon identifiers
     */
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

    /**
     * Get enum cases grouped by style.
     *
     * @return array<string, array<self>> Array of styles with their icons
     */
    public static function groupedByStyle(): array
    {
        $grouped = [];

        foreach (self::cases() as $case) {
            $parts = explode('-', $case->value, 3);
            if (count($parts) >= 2) {
                $style = $parts[1];
                if (count($parts) === 3 && $parts[2] === 'duotone') {
                    $style .= '-duotone';
                }
                $grouped[$style][] = $case;
            }
        }

        return $grouped;
    }

    /**
     * Get all icon names grouped by style
     */
    public static function getIconsByStyle(): array
    {
        $icons = [];
        foreach (self::cases() as $case) {
            $parts = explode('-', $case->value, 3);
            if (count($parts) >= 3) {
                $style = $parts[1];
                $name = $parts[2];
                $icons[$style][] = $name;
            }
        }
        return $icons;
    }
}
