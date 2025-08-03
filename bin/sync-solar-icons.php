#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Solar Icons Enum Synchronization Script
 *
 * This script automatically synchronizes the SolarIcon enum with the actual
 * icon files present in the resources/icons/solar directory.
 *
 * Features:
 * - Scans all icon files in the project directory
 * - Compares with existing enum cases
 * - Adds missing icons to the enum
 * - Removes enum cases for non-existent files
 * - Maintains consistent naming conventions
 * - Preserves existing code structure and formatting
 * - Provides detailed output of changes made
 *
 * Usage: php bin/sync-solar-icons.php [--dry-run] [--verbose]
 *
 * @package Monsefeledrisse\FilamentSolarIcons
 */

// Ensure we're running from the project root
$projectRoot = dirname(__DIR__);
chdir($projectRoot);

// Autoload dependencies
if (!file_exists($projectRoot . '/vendor/autoload.php')) {
    echo "Error: Composer dependencies not installed. Run 'composer install' first.\n";
    exit(1);
}

require_once $projectRoot . '/vendor/autoload.php';

use Monsefeledrisse\FilamentSolarIcons\SolarIconHelper;

class SolarIconSynchronizer
{
    private string $projectRoot;
    private string $enumFilePath;
    private bool $dryRun = false;
    private bool $verbose = false;
    private array $addedIcons = [];
    private array $removedIcons = [];
    private array $existingEnumCases = [];

    public function __construct(string $projectRoot)
    {
        $this->projectRoot = $projectRoot;
        $this->enumFilePath = $projectRoot . '/src/SolarIcon.php';
    }

    public function setDryRun(bool $dryRun): void
    {
        $this->dryRun = $dryRun;
    }

    public function setVerbose(bool $verbose): void
    {
        $this->verbose = $verbose;
    }

    public function synchronize(): bool
    {
        try {
            $this->log("Starting Solar Icons synchronization...\n");

            // Step 1: Validate prerequisites
            if (!$this->validatePrerequisites()) {
                return false;
            }

            // Step 2: Get all available icon files
            $this->log("Scanning icon files...");
            $iconFiles = $this->getAvailableIconFiles();
            $this->log(sprintf("Found %d icon files\n", $iconFiles->count()));

            // Step 3: Parse existing enum cases
            $this->log("Parsing existing enum cases...");
            $this->parseExistingEnumCases();
            $this->log(sprintf("Found %d existing enum cases\n", count($this->existingEnumCases)));

            // Step 4: Compare and identify differences
            $this->log("Comparing files with enum cases...");
            $this->identifyDifferences($iconFiles);

            // Step 5: Generate updated enum content
            if (!empty($this->addedIcons) || !empty($this->removedIcons)) {
                $this->log("Generating updated enum content...");
                $updatedContent = $this->generateUpdatedEnumContent($iconFiles);

                // Step 6: Write updated content (if not dry run)
                if (!$this->dryRun) {
                    // Create backup
                    $this->createBackup();

                    $this->log("Writing updated enum file...");
                    if (!file_put_contents($this->enumFilePath, $updatedContent)) {
                        throw new Exception("Failed to write updated enum file");
                    }
                }
            }

            // Step 7: Report results
            $this->reportResults();

            return true;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            if ($this->verbose) {
                echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
            }
            return false;
        }
    }

    private function validatePrerequisites(): bool
    {
        if (!file_exists($this->enumFilePath)) {
            throw new Exception("SolarIcon enum file not found: {$this->enumFilePath}");
        }

        if (!is_readable($this->enumFilePath)) {
            throw new Exception("SolarIcon enum file is not readable: {$this->enumFilePath}");
        }

        if (!is_writable($this->enumFilePath)) {
            throw new Exception("SolarIcon enum file is not writable: {$this->enumFilePath}");
        }

        $iconBasePath = $this->projectRoot . '/resources/icons/solar';
        if (!is_dir($iconBasePath)) {
            throw new Exception("Solar icons directory not found: {$iconBasePath}");
        }

        return true;
    }

    private function getAvailableIconFiles()
    {
        try {
            return SolarIconHelper::getAllIconFiles();
        } catch (Exception $e) {
            throw new Exception("Failed to scan icon files: " . $e->getMessage());
        }
    }

    private function parseExistingEnumCases(): void
    {
        $enumContent = file_get_contents($this->enumFilePath);
        if ($enumContent === false) {
            throw new Exception("Failed to read enum file");
        }

        // Parse enum cases using regex
        $pattern = '/case\s+(\w+)\s*=\s*[\'"]([^\'"]+)[\'"]\s*;/';
        if (preg_match_all($pattern, $enumContent, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $caseName = $match[1];
                $caseValue = $match[2];
                $this->existingEnumCases[$caseValue] = $caseName;
            }
        }

        $this->log(sprintf("Parsed %d existing enum cases\n", count($this->existingEnumCases)), true);
    }

    private function identifyDifferences($iconFiles): void
    {
        // Generate expected blade-ui-kit icon names
        $expectedBladeIconNames = [];
        foreach ($iconFiles as $icon) {
            $bladeIconName = $icon['style'] . '-' . $icon['name'];
            $expectedBladeIconNames[] = $bladeIconName;
        }

        $enumKeys = array_keys($this->existingEnumCases);

        // Find missing icons (in files but not in enum)
        $this->addedIcons = array_diff($expectedBladeIconNames, $enumKeys);

        // Find orphaned enum cases (in enum but not in files)
        $this->removedIcons = array_diff($enumKeys, $expectedBladeIconNames);

        $this->log(sprintf("Icons to add: %d\n", count($this->addedIcons)), true);
        $this->log(sprintf("Icons to remove: %d\n", count($this->removedIcons)), true);

        if ($this->verbose) {
            if (!empty($this->addedIcons)) {
                $this->log("Icons to add:\n");
                $count = 0;
                foreach ($this->addedIcons as $icon) {
                    if ($count < 20) {
                        $this->log("  + {$icon}\n");
                        $count++;
                    } else {
                        $remaining = count($this->addedIcons) - $count;
                        $this->log("  ... and {$remaining} more icons\n");
                        break;
                    }
                }
            }

            if (!empty($this->removedIcons)) {
                $this->log("Icons to remove:\n");
                $count = 0;
                foreach ($this->removedIcons as $icon) {
                    if ($count < 20) {
                        $this->log("  - {$icon}\n");
                        $count++;
                    } else {
                        $remaining = count($this->removedIcons) - $count;
                        $this->log("  ... and {$remaining} more icons\n");
                        break;
                    }
                }
            }
        }
    }

    private function generateUpdatedEnumContent($iconFiles): string
    {
        // Generate new enum cases for all icons
        $newEnumCases = $this->generateEnumCases($iconFiles);

        // Build the complete new enum file content
        $content = "<?php\n\n";
        $content .= "declare(strict_types=1);\n\n";
        $content .= "namespace Monsefeledrisse\\FilamentSolarIcons;\n\n";
        $content .= "/**\n";
        $content .= " * Solar Icon Set for Filament v4\n";
        $content .= " *\n";
        $content .= " * This enum provides type-safe access to Solar icons in Filament v4,\n";
        $content .= " * similar to how Heroicon works in the core Filament package.\n";
        $content .= " *\n";
        $content .= " * Each enum case represents a specific Solar icon with its full identifier.\n";
        $content .= " * The enum implements ScalableIcon when Filament is available.\n";
        $content .= " *\n";
        $content .= " * @example\n";
        $content .= " * ```php\n";
        $content .= " * // In Filament components\n";
        $content .= " * Action::make('star')->icon(SolarIcon::Star)\n";
        $content .= " * TextInput::make('name')->prefixIcon(SolarIcon::OutlineUser)\n";
        $content .= " * NavigationItem::make('Dashboard')->icon(SolarIcon::LinearHome)\n";
        $content .= " * ```\n";
        $content .= " *\n";
        $content .= " * @package Monsefeledrisse\\FilamentSolarIcons\n";
        $content .= " */\n";
        $content .= "enum SolarIcon: string\n";
        $content .= "{\n";

        // Add header comment for generated cases
        $content .= "    // ========================================\n";
        $content .= "    // AUTO-GENERATED ENUM CASES\n";
        $content .= "    // Generated by: bin/sync-solar-icons.php\n";
        $content .= "    // Total icons: " . $iconFiles->count() . "\n";
        $content .= "    // Generated on: " . date('Y-m-d H:i:s') . "\n";
        $content .= "    // ========================================\n\n";

        $content .= $newEnumCases . "\n\n";

        // Add the methods
        $content .= $this->getEnumMethods();

        $content .= "}\n";

        return $content;
    }

    private function generateEnumCases($iconFiles): string
    {
        $casesByStyle = [];
        $usedNames = [];
        $usedValues = []; // Track used values to prevent duplicates

        // Group icons by style
        foreach ($iconFiles as $icon) {
            $style = $icon['style'];
            $iconKey = $icon['key'];

            // Skip if this icon value is already used (PHP enums can't have duplicate values)
            if (in_array($iconKey, $usedValues, true)) {
                continue;
            }

            if (!isset($casesByStyle[$style])) {
                $casesByStyle[$style] = [];
            }

            $enumName = $this->sanitizeEnumName(SolarIconHelper::normalizeIconName($icon['name']));

            // Handle duplicates by adding style prefix
            if (in_array($enumName, $usedNames, true)) {
                $stylePrefix = $this->getStylePrefix($icon['style']);
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
            $usedValues[] = $iconKey;

            // Generate the correct blade-ui-kit icon name (style-filename)
            $bladeIconName = $style . '-' . $icon['name'];
            $casesByStyle[$style][] = "    case {$enumName} = '{$bladeIconName}';";
        }

        // Generate organized output
        $output = [];
        $styleOrder = ['solar-bold', 'solar-outline', 'solar-linear', 'solar-broken', 'solar-bold-duotone', 'solar-line-duotone'];

        foreach ($styleOrder as $style) {
            if (isset($casesByStyle[$style])) {
                $styleName = ucwords(str_replace(['solar-', '-'], ['', ' '], $style));
                $output[] = "    // {$styleName} Style (" . count($casesByStyle[$style]) . " icons)";
                $output = array_merge($output, $casesByStyle[$style]);
                $output[] = "";
                unset($casesByStyle[$style]);
            }
        }

        // Add any remaining styles
        foreach ($casesByStyle as $style => $cases) {
            $styleName = ucwords(str_replace(['solar-', '-'], ['', ' '], $style));
            $output[] = "    // {$styleName} Style (" . count($cases) . " icons)";
            $output = array_merge($output, $cases);
            $output[] = "";
        }

        return implode("\n", array_filter($output));
    }

    private function getStylePrefix(string $style): string
    {
        $prefix = str_replace(['solar-', '-'], ['', ''], $style);
        return ucfirst($prefix);
    }

    private function sanitizeEnumName(string $name): string
    {
        // Remove or replace problematic characters that could break PHP enum syntax
        $name = str_replace(['(', ')', '[', ']', '{', '}', '"', "'", '\\', '/', '|', '&', '%', '$', '#', '@', '!', '?', '*', '+', '=', '<', '>', '~', '`'], '', $name);

        // Replace multiple spaces/underscores with single ones
        $name = preg_replace('/[_\s]+/', '', $name);

        // Ensure it starts with a letter (PHP enum case requirement)
        if (!empty($name) && is_numeric($name[0])) {
            $name = 'Icon' . $name;
        }

        // Ensure it's not empty
        if (empty($name)) {
            $name = 'UnknownIcon';
        }

        return $name;
    }

    private function getEnumMethods(): string
    {
        return '    /**
     * Get the icon for a specific size.
     *
     * This method provides compatibility with Filament\'s ScalableIcon interface.
     * For Solar icons, we return the same icon regardless of size
     * since they are SVG and scale naturally.
     *
     * @param string $size The requested icon size (ignored for SVG icons)
     * @return string The icon identifier
     */
    public function getIconForSize(string $size): string
    {
        return $this->value;
    }

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
            \'bold\' => \'Bold - Filled, strong visual weight\',
            \'bold-duotone\' => \'Bold Duotone - Two-tone bold style\',
            \'broken\' => \'Broken - Stylized broken line style\',
            \'line-duotone\' => \'Line Duotone - Two-tone line style\',
            \'linear\' => \'Linear - Clean, minimal lines\',
            \'outline\' => \'Outline - Clean outlined style\',
        ];
    }

    /**
     * Get icon identifier by name and style.
     *
     * @param string $name The icon name
     * @param string $style The icon style (default: \'linear\')
     * @return string The full icon identifier
     */
    public static function getIcon(string $name, string $style = \'linear\'): string
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
            $parts = explode(\'-\', $case->value, 3);
            if (count($parts) >= 2) {
                $style = $parts[1];
                if (count($parts) === 3 && $parts[2] === \'duotone\') {
                    $style .= \'-duotone\';
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
            $parts = explode(\'-\', $case->value, 3);
            if (count($parts) >= 3) {
                $style = $parts[1];
                $name = $parts[2];
                $icons[$style][] = $name;
            }
        }
        return $icons;
    }

';
    }

    private function reportResults(): void
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "SYNCHRONIZATION RESULTS\n";
        echo str_repeat("=", 60) . "\n";

        if (empty($this->addedIcons) && empty($this->removedIcons)) {
            echo "✅ No changes needed - enum is already synchronized!\n";
        } else {
            if (!empty($this->addedIcons)) {
                echo sprintf("✅ Added %d new icon(s) to enum\n", count($this->addedIcons));
            }

            if (!empty($this->removedIcons)) {
                echo sprintf("🗑️  Removed %d orphaned enum case(s)\n", count($this->removedIcons));
            }

            if ($this->dryRun) {
                echo "\n⚠️  DRY RUN MODE - No files were modified\n";
                echo "Run without --dry-run to apply changes\n";
            } else {
                echo "\n✅ Enum file updated successfully!\n";
            }
        }

        echo str_repeat("=", 60) . "\n";
    }

    private function createBackup(): void
    {
        $backupPath = $this->enumFilePath . '.backup.' . date('Y-m-d_H-i-s');
        if (!copy($this->enumFilePath, $backupPath)) {
            throw new Exception("Failed to create backup file: {$backupPath}");
        }
        $this->log("Created backup: {$backupPath}\n");
    }

    private function log(string $message, bool $verboseOnly = false): void
    {
        if (!$verboseOnly || $this->verbose) {
            echo $message;
        }
    }
}

// Parse command line arguments
$dryRun = in_array('--dry-run', $argv);
$verbose = in_array('--verbose', $argv) || in_array('-v', $argv);
$help = in_array('--help', $argv) || in_array('-h', $argv);

if ($help) {
    echo "Solar Icons Enum Synchronization Script\n";
    echo str_repeat("=", 50) . "\n\n";
    echo "This script automatically synchronizes the SolarIcon enum with the actual\n";
    echo "icon files present in the resources/icons/solar directory.\n\n";
    echo "FEATURES:\n";
    echo "• Scans all icon files in the project directory\n";
    echo "• Compares with existing enum cases\n";
    echo "• Adds missing icons to the enum\n";
    echo "• Removes enum cases for non-existent files\n";
    echo "• Maintains consistent naming conventions\n";
    echo "• Preserves existing code structure and formatting\n";
    echo "• Creates automatic backups before making changes\n";
    echo "• Provides detailed output of changes made\n\n";
    echo "USAGE:\n";
    echo "  php bin/sync-solar-icons.php [options]\n\n";
    echo "OPTIONS:\n";
    echo "  --dry-run    Show what would be changed without modifying files\n";
    echo "  --verbose    Show detailed output including sample changes\n";
    echo "  --help       Show this help message\n\n";
    echo "EXAMPLES:\n";
    echo "  php bin/sync-solar-icons.php --dry-run --verbose\n";
    echo "  php bin/sync-solar-icons.php\n\n";
    echo "SAFETY:\n";
    echo "• Always run with --dry-run first to preview changes\n";
    echo "• Automatic backups are created before modifications\n";
    echo "• The script preserves existing enum structure and methods\n\n";
    exit(0);
}

// Run the synchronizer
$synchronizer = new SolarIconSynchronizer($projectRoot);
$synchronizer->setDryRun($dryRun);
$synchronizer->setVerbose($verbose);

$success = $synchronizer->synchronize();
exit($success ? 0 : 1);
