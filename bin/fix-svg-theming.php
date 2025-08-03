#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * SVG Theming Fix Script
 *
 * This script fixes SVG icons to properly inherit theme colors by replacing
 * hardcoded colors (fill="black", stroke="black") with currentColor.
 *
 * This allows icons to dynamically inherit colors from Filament's theming system
 * instead of always displaying as black.
 *
 * Usage: php bin/fix-svg-theming.php [--dry-run] [--verbose] [--file=path]
 *
 * @package Monsefeledrisse\FilamentSolarIcons
 */

class SvgThemingFixer
{
    private bool $dryRun = false;
    private bool $verbose = false;
    private string $iconsPath;
    private int $processedFiles = 0;
    private int $modifiedFiles = 0;
    private array $modifications = [];

    public function __construct(string $projectRoot)
    {
        $this->iconsPath = $projectRoot . '/resources/icons/solar';
    }

    public function setDryRun(bool $dryRun): void
    {
        $this->dryRun = $dryRun;
    }

    public function setVerbose(bool $verbose): void
    {
        $this->verbose = $verbose;
    }

    public function fixAllIcons(): bool
    {
        try {
            $this->log("Starting SVG theming fix...");
            $this->log("Icons path: {$this->iconsPath}");
            
            if ($this->dryRun) {
                $this->log("DRY RUN MODE - No files will be modified");
            }

            if (!is_dir($this->iconsPath)) {
                throw new Exception("Icons directory not found: {$this->iconsPath}");
            }

            $this->processDirectory($this->iconsPath);
            $this->reportResults();

            return true;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            return false;
        }
    }

    public function fixSingleFile(string $filePath): bool
    {
        try {
            if (!file_exists($filePath)) {
                throw new Exception("File not found: {$filePath}");
            }

            if (!str_ends_with($filePath, '.svg')) {
                throw new Exception("File is not an SVG: {$filePath}");
            }

            $this->log("Processing single file: {$filePath}");
            
            if ($this->dryRun) {
                $this->log("DRY RUN MODE - File will not be modified");
            }

            $this->processFile($filePath);
            $this->reportResults();

            return true;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
            return false;
        }
    }

    private function processDirectory(string $dir): void
    {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'svg') {
                $this->processFile($file->getPathname());
            }
        }
    }

    private function processFile(string $filePath): void
    {
        $this->processedFiles++;
        
        $content = file_get_contents($filePath);
        if ($content === false) {
            $this->log("Warning: Could not read file: {$filePath}");
            return;
        }

        $originalContent = $content;
        $modifications = [];

        // Replace hardcoded fill colors
        $fillPattern = '/fill="(#[0-9a-fA-F]{3,6}|black|white|rgb\([^)]+\)|rgba\([^)]+\))"/';
        if (preg_match($fillPattern, $content)) {
            $content = preg_replace($fillPattern, 'fill="currentColor"', $content);
            $modifications[] = 'fill attributes';
        }

        // Replace hardcoded stroke colors
        $strokePattern = '/stroke="(#[0-9a-fA-F]{3,6}|black|white|rgb\([^)]+\)|rgba\([^)]+\))"/';
        if (preg_match($strokePattern, $content)) {
            $content = preg_replace($strokePattern, 'stroke="currentColor"', $content);
            $modifications[] = 'stroke attributes';
        }

        // Check if any modifications were made
        if ($content !== $originalContent) {
            $this->modifiedFiles++;
            $relativePath = str_replace($this->iconsPath . '/', '', $filePath);
            $this->modifications[$relativePath] = $modifications;

            if ($this->verbose) {
                $this->log("Modified: {$relativePath} (" . implode(', ', $modifications) . ")");
            }

            // Write the modified content (unless dry run)
            if (!$this->dryRun) {
                if (file_put_contents($filePath, $content) === false) {
                    $this->log("Warning: Could not write file: {$filePath}");
                }
            }
        }
    }

    private function reportResults(): void
    {
        echo "\n" . str_repeat("=", 50) . "\n";
        echo "SVG THEMING FIX RESULTS\n";
        echo str_repeat("=", 50) . "\n";
        
        echo "Files processed: {$this->processedFiles}\n";
        echo "Files modified: {$this->modifiedFiles}\n";
        
        if ($this->dryRun && $this->modifiedFiles > 0) {
            echo "\nDRY RUN - No files were actually modified\n";
        }

        if ($this->verbose && !empty($this->modifications)) {
            echo "\nDetailed modifications:\n";
            foreach ($this->modifications as $file => $mods) {
                echo "  • {$file}: " . implode(', ', $mods) . "\n";
            }
        }

        if ($this->modifiedFiles > 0) {
            echo "\n✅ Icons will now inherit theme colors using currentColor\n";
            echo "🎨 Icons should now respond to Filament's theming system\n";
        } else {
            echo "\n✅ No modifications needed - icons already use currentColor\n";
        }
    }

    private function log(string $message): void
    {
        if ($this->verbose) {
            echo $message . "\n";
        }
    }
}

// Parse command line arguments
$options = getopt('', ['dry-run', 'verbose', 'file:', 'help']);
$dryRun = isset($options['dry-run']);
$verbose = isset($options['verbose']);
$singleFile = $options['file'] ?? null;
$help = isset($options['help']);

// Get project root directory
$projectRoot = dirname(__DIR__);

if ($help) {
    echo "SVG Theming Fix Script\n";
    echo str_repeat("=", 50) . "\n\n";
    echo "This script fixes SVG icons to properly inherit theme colors by replacing\n";
    echo "hardcoded colors (fill=\"black\", stroke=\"black\") with currentColor.\n\n";
    echo "USAGE:\n";
    echo "  php bin/fix-svg-theming.php [options]\n\n";
    echo "OPTIONS:\n";
    echo "  --dry-run       Show what would be changed without modifying files\n";
    echo "  --verbose       Show detailed output\n";
    echo "  --file=path     Process only a specific file\n";
    echo "  --help          Show this help message\n\n";
    echo "EXAMPLES:\n";
    echo "  php bin/fix-svg-theming.php --dry-run --verbose\n";
    echo "  php bin/fix-svg-theming.php --file=resources/icons/solar/solar-bold/Arrows/alt_arrow_down.svg\n";
    echo "  php bin/fix-svg-theming.php\n\n";
    exit(0);
}

// Run the fixer
$fixer = new SvgThemingFixer($projectRoot);
$fixer->setDryRun($dryRun);
$fixer->setVerbose($verbose);

if ($singleFile) {
    $success = $fixer->fixSingleFile($singleFile);
} else {
    $success = $fixer->fixAllIcons();
}

exit($success ? 0 : 1);
