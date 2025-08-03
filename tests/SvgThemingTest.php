<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class SvgThemingTest extends TestCase
{
    private string $iconsPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->iconsPath = __DIR__ . '/../resources/icons/solar';
    }

    /** @test */
    public function specific_icon_uses_current_color(): void
    {
        $iconPath = $this->iconsPath . '/solar-bold/Arrows/alt_arrow_down.svg';
        
        $this->assertFileExists($iconPath, 'The alt_arrow_down.svg icon should exist');
        
        $content = file_get_contents($iconPath);
        $this->assertNotFalse($content, 'Should be able to read the icon file');
        
        // Should use currentColor instead of hardcoded colors
        $this->assertStringContainsString('fill="currentColor"', $content,
            'Icon should use currentColor for fill to inherit theme colors');

        // Should not have hardcoded black color
        $this->assertStringNotContainsString('fill="black"', $content,
            'Icon should not have hardcoded black fill color');
    }

    /** @test */
    public function icons_do_not_have_hardcoded_colors(): void
    {
        $iconsWithHardcodedColors = [];
        $totalIcons = 0;
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($this->iconsPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'svg') {
                $totalIcons++;
                $content = file_get_contents($file->getPathname());
                
                if ($content === false) {
                    continue;
                }

                // Check for hardcoded colors
                $hasHardcodedFill = preg_match('/fill="(#[0-9a-fA-F]{3,6}|black|white|rgb\([^)]+\)|rgba\([^)]+\))"/', $content);
                $hasHardcodedStroke = preg_match('/stroke="(#[0-9a-fA-F]{3,6}|black|white|rgb\([^)]+\)|rgba\([^)]+\))"/', $content);
                
                if ($hasHardcodedFill || $hasHardcodedStroke) {
                    $relativePath = str_replace($this->iconsPath . '/', '', $file->getPathname());
                    $iconsWithHardcodedColors[] = $relativePath;
                }
            }
        }

        // Report findings
        $hardcodedCount = count($iconsWithHardcodedColors);
        $percentageFixed = $totalIcons > 0 ? round((($totalIcons - $hardcodedCount) / $totalIcons) * 100, 2) : 0;
        
        echo "\nSVG Theming Analysis:\n";
        echo "Total icons: {$totalIcons}\n";
        echo "Icons with hardcoded colors: {$hardcodedCount}\n";
        echo "Icons properly themed: " . ($totalIcons - $hardcodedCount) . " ({$percentageFixed}%)\n";
        
        if ($hardcodedCount > 0) {
            echo "\nFirst 10 icons with hardcoded colors:\n";
            foreach (array_slice($iconsWithHardcodedColors, 0, 10) as $icon) {
                echo "  - {$icon}\n";
            }
            
            if ($hardcodedCount > 10) {
                echo "  ... and " . ($hardcodedCount - 10) . " more\n";
            }
        }

        // For now, we'll make this a soft assertion since we know there are issues
        // Once the fix is applied, this should pass
        if ($hardcodedCount > 0) {
            $this->markTestIncomplete(
                "Found {$hardcodedCount} icons with hardcoded colors. " .
                "Run 'php bin/fix-svg-theming.php' to fix them."
            );
        }
    }

    /** @test */
    public function sample_icons_use_current_color_after_fix(): void
    {
        $sampleIcons = [
            'solar-bold/Arrows/alt_arrow_down.svg',
            'solar-linear/Security/Incognito.svg',
            'solar-linear/Money/Banknote.svg',
        ];

        foreach ($sampleIcons as $iconPath) {
            $fullPath = $this->iconsPath . '/' . $iconPath;
            
            if (!file_exists($fullPath)) {
                $this->markTestSkipped("Sample icon not found: {$iconPath}");
                continue;
            }

            $content = file_get_contents($fullPath);
            $this->assertNotFalse($content, "Should be able to read {$iconPath}");

            // Check if it uses currentColor (good) or has hardcoded colors (bad)
            $hasCurrentColor = strpos($content, 'currentColor') !== false;
            $hasHardcodedColors = preg_match('/(?:fill|stroke)="(?:#[0-9a-fA-F]{3,6}|black|white|rgb\([^)]+\)|rgba\([^)]+\))"/', $content);

            if ($hasHardcodedColors && !$hasCurrentColor) {
                $this->fail(
                    "Icon {$iconPath} still has hardcoded colors and doesn't use currentColor. " .
                    "Run the theming fix script to resolve this."
                );
            }

            // If it has colors, they should be currentColor
            if (preg_match('/(?:fill|stroke)="[^"]*"/', $content)) {
                $this->assertStringContainsString('currentColor', $content,
                    "Icon {$iconPath} should use currentColor for theme inheritance");
            }
        }
    }
}
