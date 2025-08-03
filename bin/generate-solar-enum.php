#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Solar Icon Enum Generator
 * 
 * This script automatically generates the SolarIcon enum by scanning all Solar icon
 * directories and creating proper PHP enum cases with correct string values.
 * 
 * Usage: php bin/generate-solar-enum.php
 */

require_once __DIR__ . '/../vendor/autoload.php';

class SolarIconEnumGenerator
{
    private const ICON_SETS = [
        'solar-bold',
        'solar-bold-duotone',
        'solar-broken',
        'solar-line-duotone',
        'solar-linear',
        'solar-outline',
    ];

    private const ICONS_BASE_PATH = __DIR__ . '/../resources/icons/solar';
    private const ENUM_OUTPUT_PATH = __DIR__ . '/../src/SolarIcon.php';

    private array $allIcons = [];
    private array $enumCases = [];

    public function generate(): void
    {
        echo "🔍 Scanning Solar icon directories...\n";
        $this->scanIconDirectories();

        echo "📝 Generating enum cases...\n";
        $this->generateEnumCases();

        echo "💾 Writing SolarIcon.php file...\n";
        $this->writeEnumFile();

        echo "✅ Successfully generated SolarIcon enum with " . count($this->enumCases) . " cases!\n";
        echo "📊 Icon distribution:\n";
        $this->printIconStats();
    }

    private function scanIconDirectories(): void
    {
        foreach (self::ICON_SETS as $iconSet) {
            $iconPath = self::ICONS_BASE_PATH . '/' . $iconSet;
            
            if (!is_dir($iconPath)) {
                echo "⚠️  Warning: Icon set directory not found: {$iconPath}\n";
                continue;
            }

            $icons = $this->scanDirectoryRecursively($iconPath);
            $this->allIcons[$iconSet] = $icons;
            
            echo "   Found " . count($icons) . " icons in {$iconSet}\n";
        }
    }

    private function scanDirectoryRecursively(string $directory): array
    {
        $icons = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && strtolower($file->getExtension()) === 'svg') {
                $filename = $file->getBasename('.svg');
                $icons[] = $filename;
            }
        }

        sort($icons);
        return array_unique($icons);
    }

    private function generateEnumCases(): void
    {
        $allUniqueIcons = [];

        // Collect all unique icon names across all sets
        foreach ($this->allIcons as $iconSet => $icons) {
            foreach ($icons as $iconName) {
                $allUniqueIcons[$iconName] = true;
            }
        }

        // Generate enum cases for each unique icon
        foreach (array_keys($allUniqueIcons) as $iconName) {
            $enumCaseName = $this->generateEnumCaseName($iconName);
            
            // Find which sets contain this icon
            $availableSets = [];
            foreach ($this->allIcons as $iconSet => $icons) {
                if (in_array($iconName, $icons)) {
                    $availableSets[] = $iconSet;
                }
            }

            // Use the first available set as the primary value
            $primarySet = $availableSets[0];
            $enumValue = $primarySet . '-' . $iconName;

            $this->enumCases[] = [
                'case_name' => $enumCaseName,
                'value' => $enumValue,
                'icon_name' => $iconName,
                'available_sets' => $availableSets,
            ];
        }

        // Sort by case name for consistent output
        usort($this->enumCases, fn($a, $b) => strcmp($a['case_name'], $b['case_name']));
    }

    private function generateEnumCaseName(string $iconName): string
    {
        // Convert filename to valid PHP enum case name
        $caseName = $iconName;
        
        // Replace common separators with underscores
        $caseName = str_replace(['-', '.', ' ', ','], '_', $caseName);
        
        // Remove any remaining non-alphanumeric characters except underscores
        $caseName = preg_replace('/[^a-zA-Z0-9_]/', '', $caseName);
        
        // Split by underscores and convert to PascalCase
        $parts = explode('_', $caseName);
        $parts = array_map('ucfirst', $parts);
        $caseName = implode('', $parts);
        
        // Ensure it starts with a letter (PHP requirement)
        if (preg_match('/^[0-9]/', $caseName)) {
            $caseName = 'Icon' . $caseName;
        }
        
        // Handle empty or invalid names
        if (empty($caseName) || !preg_match('/^[a-zA-Z][a-zA-Z0-9]*$/', $caseName)) {
            $caseName = 'Icon' . md5($iconName);
        }

        return $caseName;
    }

    private function writeEnumFile(): void
    {
        $enumContent = $this->generateEnumFileContent();
        
        if (file_put_contents(self::ENUM_OUTPUT_PATH, $enumContent) === false) {
            throw new RuntimeException('Failed to write enum file: ' . self::ENUM_OUTPUT_PATH);
        }
    }

    private function generateEnumFileContent(): string
    {
        $cases = [];
        foreach ($this->enumCases as $enumCase) {
            $caseName = $enumCase['case_name'];
            $value = $enumCase['value'];
            $iconName = $enumCase['icon_name'];
            $availableSets = implode(', ', $enumCase['available_sets']);

            $cases[] = "    case {$caseName} = '{$value}'; // {$iconName} (available in: {$availableSets})";
        }

        $casesString = implode("\n", $cases);

        // Generate match cases for getAvailableSets method
        $matchCases = [];
        foreach ($this->enumCases as $enumCase) {
            $caseName = $enumCase['case_name'];
            $availableSets = array_map(fn($set) => "'{$set}'", $enumCase['available_sets']);
            $setsArray = '[' . implode(', ', $availableSets) . ']';
            $matchCases[] = "            self::{$caseName} => {$setsArray},";
        }
        $matchCasesString = implode("\n", $matchCases);

        $totalCases = count($this->enumCases);
        $timestamp = date('Y-m-d H:i:s');

        return <<<PHP
<?php

declare(strict_types=1);

namespace Monsefeledrisse\FilamentSolarIcons;

/**
 * Solar Icon Enum
 *
 * This enum contains all available Solar icons across all icon sets.
 * Each case represents a unique icon with its corresponding BladeUI Icons identifier.
 *
 * Usage:
 * - In Blade: <x-icon :name="SolarIcon::Home->value" />
 * - With @svg: @svg(SolarIcon::Home->value)
 * - In PHP: SolarIcon::Home->value returns 'solar-linear-home'
 *
 * Generated automatically on {$timestamp}
 * Total icons: {$totalCases}
 *
 * @package Monsefeledrisse\FilamentSolarIcons
 */
enum SolarIcon: string
{
{$casesString}

    /**
     * Get all available icon sets for this icon.
     */
    public function getAvailableSets(): array
    {
        return match (\$this) {
{$matchCasesString}
        };
    }

    /**
     * Get the icon name without the set prefix.
     */
    public function getIconName(): string
    {
        return substr(\$this->value, strpos(\$this->value, '-', 6) + 1);
    }

    /**
     * Get the primary icon set for this icon.
     */
    public function getPrimarySet(): string
    {
        return substr(\$this->value, 0, strpos(\$this->value, '-', 6));
    }

    /**
     * Check if this icon is available in a specific set.
     */
    public function isAvailableIn(string \$set): bool
    {
        return in_array(\$set, \$this->getAvailableSets());
    }

    /**
     * Get the icon value for a specific set.
     */
    public function forSet(string \$set): string
    {
        if (!\$this->isAvailableIn(\$set)) {
            throw new \InvalidArgumentException("Icon {\$this->name} is not available in set {\$set}");
        }

        return \$set . '-' . \$this->getIconName();
    }
}
PHP;
    }

    private function printIconStats(): void
    {
        foreach ($this->allIcons as $iconSet => $icons) {
            echo "   {$iconSet}: " . count($icons) . " icons\n";
        }
    }
}

// Run the generator
try {
    $generator = new SolarIconEnumGenerator();
    $generator->generate();
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
