<?php

declare(strict_types=1);

namespace Monsefeledrisse\FilamentSolarIcons\Tests;

use BladeUI\Icons\Factory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Monsefeledrisse\FilamentSolarIcons\SolarIcon;
use Throwable;

/**
 * Test Controller for Solar Icons Integration Testing
 * 
 * This controller provides web routes for testing Solar Icons integration
 * with blade-ui-kit/blade-icons package in a real Laravel environment.
 */
class TestController extends Controller
{
    /**
     * Display the main test page with various Solar icons
     */
    public function index(Request $request)
    {
        try {
            $testData = $this->prepareTestData();
            
            return view('solar-icons-test', $testData);
        } catch (Throwable $e) {
            return $this->errorResponse('Failed to load test page', $e);
        }
    }

    /**
     * Test specific icon rendering via AJAX
     */
    public function testIcon(Request $request)
    {
        $iconName = $request->input('icon', '');
        
        if (empty($iconName)) {
            return response()->json([
                'success' => false,
                'error' => 'Icon name is required'
            ], 400);
        }

        try {
            $html = view('solar-icon-test-single', ['iconName' => $iconName])->render();
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'iconName' => $iconName
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'iconName' => $iconName
            ], 500);
        }
    }

    /**
     * Get information about registered icon sets
     */
    public function iconSets(Request $request)
    {
        try {
            $factory = app(Factory::class);
            $registeredSets = $factory->all();
            
            $solarSets = array_filter($registeredSets, function ($key) {
                return str_starts_with($key, 'solar-');
            }, ARRAY_FILTER_USE_KEY);

            $setInfo = [];
            foreach ($solarSets as $setName => $setConfig) {
                $paths = $setConfig['paths'] ?? [];
                $iconCount = 0;
                
                foreach ($paths as $path) {
                    if (is_dir($path)) {
                        $iconCount += count(glob($path . '/*.svg'));
                    }
                }
                
                $setInfo[$setName] = [
                    'name' => $setName,
                    'prefix' => $setConfig['prefix'] ?? $setName,
                    'paths' => $paths,
                    'iconCount' => $iconCount,
                    'enabled' => true
                ];
            }

            return response()->json([
                'success' => true,
                'iconSets' => $setInfo,
                'totalSets' => count($setInfo)
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test enum integration
     */
    public function testEnum(Request $request)
    {
        try {
            $enumCases = SolarIcon::cases();
            $sampleSize = min(20, count($enumCases));
            $sampleCases = array_slice($enumCases, 0, $sampleSize);
            
            $enumData = [];
            foreach ($sampleCases as $case) {
                $enumData[] = [
                    'name' => $case->name,
                    'value' => $case->value,
                    'exists' => SolarIcon::exists($case->value)
                ];
            }

            return response()->json([
                'success' => true,
                'totalEnumCases' => count($enumCases),
                'sampleCases' => $enumData,
                'enumMethods' => [
                    'exists' => method_exists(SolarIcon::class, 'exists'),
                    'values' => method_exists(SolarIcon::class, 'values'),
                    'groupedByStyle' => method_exists(SolarIcon::class, 'groupedByStyle'),
                    'getAvailableStyles' => method_exists(SolarIcon::class, 'getAvailableStyles'),
                ]
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Prepare test data for the main test page
     */
    private function prepareTestData(): array
    {
        // Sample icons for testing different styles
        $testIcons = [
            'bold' => [
                'solar-bold-Home',
                'solar-bold-User', 
                'solar-bold-Star',
                'solar-bold-Heart',
                'solar-bold-Settings'
            ],
            'outline' => [
                'solar-outline-Home',
                'solar-outline-User',
                'solar-outline-Star', 
                'solar-outline-Heart',
                'solar-outline-Settings'
            ],
            'linear' => [
                'solar-linear-Home',
                'solar-linear-User',
                'solar-linear-Star',
                'solar-linear-Heart', 
                'solar-linear-Settings'
            ],
            'broken' => [
                'solar-broken-Home',
                'solar-broken-User',
                'solar-broken-Star',
                'solar-broken-Heart',
                'solar-broken-Settings'
            ]
        ];

        // Sample enum cases
        $enumCases = array_slice(SolarIcon::cases(), 0, 10);
        
        // Error test cases
        $errorTestCases = [
            'solar-bold-NonExistentIcon',
            'invalid-icon-name',
            'solar-invalid-format',
            ''
        ];

        // Icon factory information
        $factory = app(Factory::class);
        $registeredSets = $factory->all();
        $solarSets = array_filter($registeredSets, function ($key) {
            return str_starts_with($key, 'solar-');
        }, ARRAY_FILTER_USE_KEY);

        return [
            'testIcons' => $testIcons,
            'enumCases' => $enumCases,
            'errorTestCases' => $errorTestCases,
            'registeredSets' => array_keys($solarSets),
            'totalEnumCases' => count(SolarIcon::cases()),
            'availableStyles' => SolarIcon::getAvailableStyles(),
            'pageTitle' => 'Solar Icons Integration Test'
        ];
    }

    /**
     * Generate error response
     */
    private function errorResponse(string $message, Throwable $e): Response
    {
        $errorData = [
            'error' => $message,
            'exception' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];

        if (config('app.debug')) {
            $errorData['trace'] = $e->getTraceAsString();
        }

        return response()->view('solar-icons-error', $errorData, 500);
    }
}
