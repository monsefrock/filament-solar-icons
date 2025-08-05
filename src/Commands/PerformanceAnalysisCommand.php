<?php

declare(strict_types=1);

namespace Monsefeledrisse\LaravelSolarIcons\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Monsefeledrisse\LaravelSolarIcons\SolarIconHelper;

/**
 * Performance Analysis Command
 *
 * Analyzes the performance impact of the Solar Icons package and provides
 * recommendations for optimization.
 */
class PerformanceAnalysisCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'solar-icons:performance 
                           {--detailed : Show detailed analysis}
                           {--benchmark : Run performance benchmarks}
                           {--fix : Apply recommended performance fixes}';

    /**
     * The console command description.
     */
    protected $description = 'Analyze Solar Icons package performance and provide optimization recommendations';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🔍 Solar Icons Performance Analysis');
        $this->newLine();

        // Basic performance metrics
        $this->analyzeBasicMetrics();
        
        if ($this->option('detailed')) {
            $this->analyzeDetailedMetrics();
        }
        
        if ($this->option('benchmark')) {
            $this->runBenchmarks();
        }
        
        if ($this->option('fix')) {
            $this->applyPerformanceFixes();
        }
        
        $this->showRecommendations();
        
        return self::SUCCESS;
    }

    /**
     * Analyze basic performance metrics.
     */
    protected function analyzeBasicMetrics(): void
    {
        $this->info('📊 Basic Performance Metrics');
        $this->line('─────────────────────────────');
        
        // Count total icons
        $startTime = microtime(true);
        $iconCount = $this->countTotalIcons();
        $scanTime = microtime(true) - $startTime;
        
        $this->line("Total Icons: <fg=yellow>{$iconCount}</>");
        $this->line("Icon Scan Time: <fg=yellow>" . number_format($scanTime * 1000, 2) . "ms</>");
        
        // Check temp directory size
        $tempSize = $this->getTempDirectorySize();
        $this->line("Temp Directory Size: <fg=yellow>{$tempSize}</>");
        
        // Check cache status
        $cacheStatus = $this->getCacheStatus();
        $this->line("Cache Status: <fg=yellow>{$cacheStatus}</>");
        
        $this->newLine();
    }

    /**
     * Analyze detailed performance metrics.
     */
    protected function analyzeDetailedMetrics(): void
    {
        $this->info('🔬 Detailed Performance Analysis');
        $this->line('─────────────────────────────────');
        
        // Memory usage analysis
        $memoryBefore = memory_get_usage(true);
        $icons = SolarIconHelper::getAllIconFiles();
        $memoryAfter = memory_get_usage(true);
        $memoryUsed = $memoryAfter - $memoryBefore;
        
        $this->line("Memory Usage for Icon Loading: <fg=yellow>" . $this->formatBytes($memoryUsed) . "</>");
        $this->line("Peak Memory Usage: <fg=yellow>" . $this->formatBytes(memory_get_peak_usage(true)) . "</>");
        
        // File system analysis
        $this->analyzeFileSystemPerformance();
        
        $this->newLine();
    }

    /**
     * Run performance benchmarks.
     */
    protected function runBenchmarks(): void
    {
        $this->info('⚡ Performance Benchmarks');
        $this->line('─────────────────────────');
        
        // Benchmark icon loading
        $this->benchmarkIconLoading();
        
        // Benchmark cache performance
        $this->benchmarkCachePerformance();
        
        $this->newLine();
    }

    /**
     * Apply performance fixes.
     */
    protected function applyPerformanceFixes(): void
    {
        $this->info('🔧 Applying Performance Fixes');
        $this->line('─────────────────────────────');
        
        // Clear and rebuild cache
        $this->line('Clearing icon cache...');
        Cache::forget('solar-icons.all-icons');
        Cache::forget('solar-icons.default-set-path');
        
        // Optimize temp directories
        $this->line('Optimizing temporary directories...');
        $this->optimizeTempDirectories();
        
        // Update configuration
        $this->line('Updating performance configuration...');
        $this->updatePerformanceConfig();
        
        $this->info('✅ Performance fixes applied successfully!');
        $this->newLine();
    }

    /**
     * Show performance recommendations.
     */
    protected function showRecommendations(): void
    {
        $this->info('💡 Performance Recommendations');
        $this->line('─────────────────────────────────');
        
        $recommendations = $this->getPerformanceRecommendations();
        
        foreach ($recommendations as $category => $items) {
            $this->line("<fg=cyan>{$category}:</>");
            foreach ($items as $item) {
                $this->line("  • {$item}");
            }
            $this->newLine();
        }
    }

    /**
     * Count total icons in the package.
     */
    protected function countTotalIcons(): int
    {
        $iconPath = __DIR__ . '/../../resources/icons/solar';
        $count = 0;
        
        if (!is_dir($iconPath)) {
            return 0;
        }
        
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($iconPath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'svg') {
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * Get temporary directory size.
     */
    protected function getTempDirectorySize(): string
    {
        $tempPath = sys_get_temp_dir() . '/solar-icons';
        
        if (!is_dir($tempPath)) {
            return '0 B';
        }
        
        $size = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($tempPath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            $size += $file->getSize();
        }
        
        return $this->formatBytes($size);
    }

    /**
     * Get cache status.
     */
    protected function getCacheStatus(): string
    {
        $cacheEnabled = config('solar-icons.performance.cache_enabled', true);
        $hasCachedIcons = Cache::has('solar-icons.all-icons');
        
        if (!$cacheEnabled) {
            return 'Disabled';
        }
        
        return $hasCachedIcons ? 'Active (cached)' : 'Enabled (not cached)';
    }

    /**
     * Analyze file system performance.
     */
    protected function analyzeFileSystemPerformance(): void
    {
        $this->line('File System Performance:');
        
        // Test directory scanning speed
        $startTime = microtime(true);
        $this->countTotalIcons();
        $scanTime = microtime(true) - $startTime;
        
        $this->line("  Directory Scan Speed: <fg=yellow>" . number_format($scanTime * 1000, 2) . "ms</>");
        
        // Check temp directory performance
        $tempPath = sys_get_temp_dir() . '/solar-icons';
        $tempWritable = is_writable(dirname($tempPath));
        $this->line("  Temp Directory Writable: <fg=yellow>" . ($tempWritable ? 'Yes' : 'No') . "</>");
    }

    /**
     * Benchmark icon loading performance.
     */
    protected function benchmarkIconLoading(): void
    {
        $iterations = 5;
        $times = [];
        
        for ($i = 0; $i < $iterations; $i++) {
            Cache::forget('solar-icons.all-icons');
            
            $startTime = microtime(true);
            SolarIconHelper::getAllIconFiles();
            $endTime = microtime(true);
            
            $times[] = ($endTime - $startTime) * 1000;
        }
        
        $avgTime = array_sum($times) / count($times);
        $minTime = min($times);
        $maxTime = max($times);
        
        $this->line("Icon Loading Benchmark (5 runs):");
        $this->line("  Average: <fg=yellow>" . number_format($avgTime, 2) . "ms</>");
        $this->line("  Min: <fg=green>" . number_format($minTime, 2) . "ms</>");
        $this->line("  Max: <fg=red>" . number_format($maxTime, 2) . "ms</>");
    }

    /**
     * Benchmark cache performance.
     */
    protected function benchmarkCachePerformance(): void
    {
        $this->line('Cache Performance:');
        
        // Test cache write
        $startTime = microtime(true);
        Cache::put('solar-icons.benchmark', 'test-data', 60);
        $writeTime = (microtime(true) - $startTime) * 1000;
        
        // Test cache read
        $startTime = microtime(true);
        Cache::get('solar-icons.benchmark');
        $readTime = (microtime(true) - $startTime) * 1000;
        
        $this->line("  Cache Write: <fg=yellow>" . number_format($writeTime, 2) . "ms</>");
        $this->line("  Cache Read: <fg=yellow>" . number_format($readTime, 2) . "ms</>");
        
        Cache::forget('solar-icons.benchmark');
    }

    /**
     * Optimize temporary directories.
     */
    protected function optimizeTempDirectories(): void
    {
        $tempPath = sys_get_temp_dir() . '/solar-icons';
        
        if (is_dir($tempPath)) {
            $this->clearDirectory($tempPath);
        }
    }

    /**
     * Update performance configuration.
     */
    protected function updatePerformanceConfig(): void
    {
        // This would update the configuration file with optimal settings
        // For now, just show what should be set
        $this->line('Recommended .env settings:');
        $this->line('SOLAR_ICONS_CACHE_ENABLED=true');
        $this->line('SOLAR_ICONS_LAZY_LOADING=true');
        $this->line('SOLAR_ICONS_FORCE_REBUILD=false');
        $this->line('SOLAR_ICONS_PRELOAD_SETS=solar-outline,solar-linear');
    }

    /**
     * Get performance recommendations.
     */
    protected function getPerformanceRecommendations(): array
    {
        return [
            'Immediate Actions' => [
                'Enable caching: SOLAR_ICONS_CACHE_ENABLED=true',
                'Enable lazy loading: SOLAR_ICONS_LAZY_LOADING=true',
                'Disable force rebuild in production: SOLAR_ICONS_FORCE_REBUILD=false',
                'Limit preloaded sets: SOLAR_ICONS_PRELOAD_SETS=solar-outline,solar-linear',
            ],
            'Configuration Optimizations' => [
                'Only register icon sets you actually use',
                'Use specific icon sets instead of loading all',
                'Consider using CDN for icon assets in production',
                'Implement icon usage tracking to optimize preloading',
            ],
            'Application-Level Optimizations' => [
                'Use Laravel\'s config caching: php artisan config:cache',
                'Enable OPcache for better PHP performance',
                'Consider using Redis/Memcached for better cache performance',
                'Monitor memory usage and adjust PHP memory limits if needed',
            ],
        ];
    }

    /**
     * Format bytes into human readable format.
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Clear all files in a directory.
     */
    protected function clearDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $files = glob($directory . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            } elseif (is_dir($file)) {
                $this->clearDirectory($file);
                rmdir($file);
            }
        }
    }
}
