<?php

namespace App\Examples;

use Monsefeledrisse\LaravelSolarIcons\SolarIcon;
use Monsefeledrisse\LaravelSolarIcons\SolarIconHelper;

/**
 * Laravel Solar Icons Usage Examples
 * 
 * This file demonstrates various ways to use Solar Icons in Laravel applications.
 */
class LaravelUsageExamples
{
    /**
     * Example: Using icons in navigation menus
     */
    public function getNavigationItems(): array
    {
        return [
            [
                'label' => 'Dashboard',
                'icon' => SolarIcon::Home->value,
                'url' => route('dashboard'),
                'active' => request()->routeIs('dashboard')
            ],
            [
                'label' => 'Users',
                'icon' => SolarIcon::Users->value,
                'url' => route('users.index'),
                'active' => request()->routeIs('users.*')
            ],
            [
                'label' => 'Settings',
                'icon' => SolarIcon::Settings->value,
                'url' => route('settings'),
                'active' => request()->routeIs('settings')
            ],
            [
                'label' => 'Reports',
                'icon' => SolarIcon::Chart->value,
                'url' => route('reports.index'),
                'active' => request()->routeIs('reports.*')
            ],
        ];
    }

    /**
     * Example: Dynamic icon selection based on status
     */
    public function getStatusIcon(string $status): string
    {
        return match($status) {
            'active' => SolarIcon::CheckCircle->value,
            'inactive' => SolarIcon::CloseCircle->value,
            'pending' => SolarIcon::Clock->value,
            'warning' => SolarIcon::DangerTriangle->value,
            'error' => SolarIcon::CloseSquare->value,
            default => SolarIcon::QuestionCircle->value,
        };
    }

    /**
     * Example: Using icons in form elements
     */
    public function getFormFieldIcons(): array
    {
        return [
            'name' => SolarIcon::User->value,
            'email' => SolarIcon::Letter->value,
            'phone' => SolarIcon::Phone->value,
            'address' => SolarIcon::MapPoint->value,
            'password' => SolarIcon::Lock->value,
            'website' => SolarIcon::Global->value,
            'company' => SolarIcon::Buildings->value,
            'date' => SolarIcon::Calendar->value,
        ];
    }

    /**
     * Example: Using icon helper methods
     */
    public function demonstrateIconHelper(): array
    {
        // Search for user-related icons
        $userIcons = SolarIconHelper::searchIcons('user');

        // Get all available icons
        $allIcons = SolarIconHelper::getAllIconFiles();

        // Get icons from specific sets
        $linearIcons = $allIcons->filter(function ($icon) {
            return $icon['style'] === 'solar-linear';
        });

        return [
            'user_icons_count' => $userIcons->count(),
            'total_icons_count' => $allIcons->count(),
            'linear_icons_count' => $linearIcons->count(),
        ];
    }

    /**
     * Example: Icon usage in different contexts
     */
    public function getContextualIcons(): array
    {
        return [
            'actions' => [
                'create' => SolarIcon::OutlineAdd->value,
                'edit' => SolarIcon::OutlineEdit->value,
                'delete' => SolarIcon::OutlineDelete->value,
                'view' => SolarIcon::OutlineEye->value,
                'download' => SolarIcon::OutlineDownload->value,
                'upload' => SolarIcon::OutlineUpload->value,
            ],
            'social' => [
                'facebook' => SolarIcon::BoldFacebook->value,
                'twitter' => SolarIcon::BoldTwitter->value,
                'instagram' => SolarIcon::BoldInstagram->value,
                'linkedin' => SolarIcon::BoldLinkedin->value,
            ],
            'files' => [
                'pdf' => SolarIcon::BoldFilePdf->value,
                'image' => SolarIcon::BoldFileImage->value,
                'document' => SolarIcon::BoldFileText->value,
                'archive' => SolarIcon::BoldFileZip->value,
            ],
            'communication' => [
                'email' => SolarIcon::LinearMail->value,
                'chat' => SolarIcon::LinearChat->value,
                'phone' => SolarIcon::LinearPhone->value,
                'video_call' => SolarIcon::LinearVideoCamera->value,
            ],
        ];
    }

    /**
     * Example: Working with different icon styles
     */
    public function demonstrateIconStyles(): array
    {
        $baseIcon = SolarIcon::LinearHome;
        
        return [
            'current_style' => $baseIcon->getPrimarySet(),
            'available_sets' => $baseIcon->getAvailableSets(),
            'bold_version' => $baseIcon->forSet('solar-bold'),
            'outline_version' => $baseIcon->forSet('solar-outline'),
            'icon_name' => $baseIcon->getIconName(),
        ];
    }

    /**
     * Example: Building a component data structure
     */
    public function buildButtonComponent(string $type, string $label): array
    {
        $iconMap = [
            'primary' => SolarIcon::LinearCheckCircle->value,
            'secondary' => SolarIcon::OutlineInfo->value,
            'success' => SolarIcon::BoldCheckCircle->value,
            'danger' => SolarIcon::BoldDanger->value,
            'warning' => SolarIcon::OutlineWarning->value,
            'info' => SolarIcon::LinearInfo->value,
        ];

        return [
            'type' => $type,
            'label' => $label,
            'icon' => $iconMap[$type] ?? SolarIcon::OutlineQuestion->value,
            'classes' => $this->getButtonClasses($type),
        ];
    }

    /**
     * Helper method for button classes
     */
    private function getButtonClasses(string $type): string
    {
        return match($type) {
            'primary' => 'bg-blue-500 hover:bg-blue-600 text-white',
            'secondary' => 'bg-gray-500 hover:bg-gray-600 text-white',
            'success' => 'bg-green-500 hover:bg-green-600 text-white',
            'danger' => 'bg-red-500 hover:bg-red-600 text-white',
            'warning' => 'bg-yellow-500 hover:bg-yellow-600 text-white',
            'info' => 'bg-cyan-500 hover:bg-cyan-600 text-white',
            default => 'bg-gray-300 hover:bg-gray-400 text-gray-800',
        };
    }
}
