<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .icon-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 1rem;
        }
        .icon-card {
            @apply border border-gray-200 rounded-lg p-4 text-center hover:shadow-md transition-shadow;
        }
        .icon-display {
            @apply w-8 h-8 mx-auto mb-2;
        }
        .error-icon {
            @apply text-red-500 border-red-200 bg-red-50;
        }
        .success-icon {
            @apply text-green-600 border-green-200 bg-green-50;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $pageTitle }}</h1>
            <p class="text-gray-600">Testing Solar Icons integration with blade-ui-kit/blade-icons</p>
            
            <!-- Status Information -->
            <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-4 border">
                    <h3 class="font-semibold text-gray-900">Registered Sets</h3>
                    <p class="text-2xl font-bold text-blue-600">{{ count($registeredSets) }}</p>
                    <p class="text-sm text-gray-500">{{ implode(', ', $registeredSets) }}</p>
                </div>
                <div class="bg-white rounded-lg p-4 border">
                    <h3 class="font-semibold text-gray-900">Enum Cases</h3>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($totalEnumCases) }}</p>
                    <p class="text-sm text-gray-500">Available icon references</p>
                </div>
                <div class="bg-white rounded-lg p-4 border">
                    <h3 class="font-semibold text-gray-900">Available Styles</h3>
                    <p class="text-2xl font-bold text-purple-600">{{ count($availableStyles) }}</p>
                    <p class="text-sm text-gray-500">{{ implode(', ', array_keys($availableStyles)) }}</p>
                </div>
            </div>
        </div>

        <!-- Test Sections -->
        <div class="space-y-8">
            <!-- Blade Component Tests -->
            <section class="bg-white rounded-lg p-6 border">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Blade Component Tests (&lt;x-icon&gt;)</h2>
                <p class="text-gray-600 mb-6">Testing Solar icons using the &lt;x-icon name="icon-name" /&gt; syntax</p>
                
                @foreach($testIcons as $style => $icons)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 capitalize">{{ $style }} Style</h3>
                        <div class="icon-grid">
                            @foreach($icons as $iconName)
                                <div class="icon-card">
                                    <div class="icon-display">
                                        @try
                                            <x-icon :name="$iconName" class="w-full h-full" />
                                            <div class="mt-2 text-xs text-green-600">✓ Success</div>
                                        @catch(Exception $e)
                                            <div class="w-full h-full bg-red-100 border border-red-300 rounded flex items-center justify-center">
                                                <span class="text-red-500 text-xs">Error</span>
                                            </div>
                                            <div class="mt-2 text-xs text-red-600">✗ Failed</div>
                                        @endtry
                                    </div>
                                    <div class="text-xs text-gray-500 break-all">{{ $iconName }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </section>

            <!-- SVG Directive Tests -->
            <section class="bg-white rounded-lg p-6 border">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">SVG Directive Tests (@svg)</h2>
                <p class="text-gray-600 mb-6">Testing Solar icons using the @svg('icon-name') directive</p>
                
                <div class="icon-grid">
                    @foreach(array_slice($testIcons['bold'], 0, 5) as $iconName)
                        <div class="icon-card">
                            <div class="icon-display">
                                @try
                                    @svg($iconName, 'w-full h-full')
                                    <div class="mt-2 text-xs text-green-600">✓ Success</div>
                                @catch(Exception $e)
                                    <div class="w-full h-full bg-red-100 border border-red-300 rounded flex items-center justify-center">
                                        <span class="text-red-500 text-xs">Error</span>
                                    </div>
                                    <div class="mt-2 text-xs text-red-600">✗ Failed</div>
                                @endtry
                            </div>
                            <div class="text-xs text-gray-500 break-all">@svg('{{ $iconName }}')</div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- Enum Integration Tests -->
            <section class="bg-white rounded-lg p-6 border">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Enum Integration Tests</h2>
                <p class="text-gray-600 mb-6">Testing Solar icons using SolarIcon enum cases</p>
                
                <div class="icon-grid">
                    @foreach($enumCases as $enumCase)
                        <div class="icon-card">
                            <div class="icon-display">
                                @try
                                    <x-icon :name="$enumCase->value" class="w-full h-full" />
                                    <div class="mt-2 text-xs text-green-600">✓ Success</div>
                                @catch(Exception $e)
                                    <div class="w-full h-full bg-red-100 border border-red-300 rounded flex items-center justify-center">
                                        <span class="text-red-500 text-xs">Error</span>
                                    </div>
                                    <div class="mt-2 text-xs text-red-600">✗ Failed</div>
                                @endtry
                            </div>
                            <div class="text-xs text-gray-500">
                                <div class="font-mono">{{ $enumCase->name }}</div>
                                <div class="break-all">{{ $enumCase->value }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- Error Handling Tests -->
            <section class="bg-white rounded-lg p-6 border">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Error Handling Tests</h2>
                <p class="text-gray-600 mb-6">Testing how the system handles invalid or non-existent icons</p>
                
                <div class="icon-grid">
                    @foreach($errorTestCases as $iconName)
                        <div class="icon-card error-icon">
                            <div class="icon-display">
                                @try
                                    <x-icon :name="$iconName" class="w-full h-full" />
                                    <div class="mt-2 text-xs text-yellow-600">⚠ Unexpected Success</div>
                                @catch(Exception $e)
                                    <div class="w-full h-full bg-red-100 border border-red-300 rounded flex items-center justify-center">
                                        <span class="text-red-500 text-xs">✗</span>
                                    </div>
                                    <div class="mt-2 text-xs text-red-600">✓ Expected Error</div>
                                @endtry
                            </div>
                            <div class="text-xs text-gray-500 break-all">{{ $iconName ?: '(empty)' }}</div>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- Interactive Testing -->
            <section class="bg-white rounded-lg p-6 border">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Interactive Testing</h2>
                <p class="text-gray-600 mb-6">Test any Solar icon by entering its name</p>
                
                <div class="flex gap-4 mb-4">
                    <input 
                        type="text" 
                        id="iconInput" 
                        placeholder="Enter icon name (e.g., solar-bold-Home)"
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                    <button 
                        onclick="testCustomIcon()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Test Icon
                    </button>
                </div>
                
                <div id="customIconResult" class="hidden">
                    <div class="icon-card max-w-xs">
                        <div class="icon-display" id="customIconDisplay"></div>
                        <div class="text-xs text-gray-500" id="customIconName"></div>
                        <div class="text-xs" id="customIconStatus"></div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Footer -->
        <footer class="mt-12 text-center text-gray-500 text-sm">
            <p>Solar Icons Integration Test • Generated on {{ date('Y-m-d H:i:s') }}</p>
            <p class="mt-2">
                <a href="/solar-icons/test/icon-sets" class="text-blue-600 hover:underline">View Icon Sets Info</a> |
                <a href="/solar-icons/test/enum" class="text-blue-600 hover:underline">View Enum Info</a>
            </p>
        </footer>
    </div>

    <script>
        function testCustomIcon() {
            const input = document.getElementById('iconInput');
            const result = document.getElementById('customIconResult');
            const display = document.getElementById('customIconDisplay');
            const name = document.getElementById('customIconName');
            const status = document.getElementById('customIconStatus');
            
            const iconName = input.value.trim();
            
            if (!iconName) {
                alert('Please enter an icon name');
                return;
            }
            
            // Show loading state
            result.classList.remove('hidden');
            display.innerHTML = '<div class="animate-spin w-6 h-6 border-2 border-blue-600 border-t-transparent rounded-full mx-auto"></div>';
            name.textContent = iconName;
            status.innerHTML = '<span class="text-blue-600">Testing...</span>';
            
            // Test the icon via AJAX
            fetch('/solar-icons/test/icon', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ icon: iconName })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    display.innerHTML = data.html;
                    status.innerHTML = '<span class="text-green-600">✓ Success</span>';
                } else {
                    display.innerHTML = '<div class="w-full h-full bg-red-100 border border-red-300 rounded flex items-center justify-center"><span class="text-red-500 text-xs">Error</span></div>';
                    status.innerHTML = '<span class="text-red-600">✗ ' + (data.error || 'Failed') + '</span>';
                }
            })
            .catch(error => {
                display.innerHTML = '<div class="w-full h-full bg-red-100 border border-red-300 rounded flex items-center justify-center"><span class="text-red-500 text-xs">Error</span></div>';
                status.innerHTML = '<span class="text-red-600">✗ Network Error</span>';
            });
        }
        
        // Allow Enter key to trigger test
        document.getElementById('iconInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                testCustomIcon();
            }
        });
    </script>
</body>
</html>
