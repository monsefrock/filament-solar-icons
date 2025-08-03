<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solar Icons Test - Error</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-red-50 min-h-screen flex items-center justify-center">
    <div class="max-w-2xl mx-auto px-4">
        <div class="bg-white rounded-lg shadow-lg p-8 border border-red-200">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-red-900">Solar Icons Test Error</h1>
                    <p class="text-red-700">An error occurred while testing Solar Icons integration</p>
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h2 class="font-semibold text-red-900 mb-2">Error Message</h2>
                    <p class="text-red-800 font-mono text-sm">{{ $error }}</p>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h2 class="font-semibold text-red-900 mb-2">Exception Details</h2>
                    <p class="text-red-800 font-mono text-sm">{{ $exception }}</p>
                    <p class="text-red-600 text-sm mt-2">
                        <strong>File:</strong> {{ $file }}<br>
                        <strong>Line:</strong> {{ $line }}
                    </p>
                </div>

                @if(isset($trace) && config('app.debug'))
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h2 class="font-semibold text-red-900 mb-2">Stack Trace</h2>
                        <pre class="text-red-800 text-xs overflow-x-auto whitespace-pre-wrap">{{ $trace }}</pre>
                    </div>
                @endif

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h2 class="font-semibold text-yellow-900 mb-2">Troubleshooting Tips</h2>
                    <ul class="text-yellow-800 text-sm space-y-1">
                        <li>• Ensure blade-ui-kit/blade-icons is installed and configured</li>
                        <li>• Check that the SolarIconSetServiceProvider is registered</li>
                        <li>• Verify that Solar icon files exist in the resources/icons/solar directory</li>
                        <li>• Run <code class="bg-yellow-100 px-1 rounded">php artisan package:discover</code> to refresh package discovery</li>
                        <li>• Check Laravel logs for additional error details</li>
                    </ul>
                </div>
            </div>

            <div class="mt-6 flex gap-4">
                <a href="/" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                    ← Back to Home
                </a>
                <button onclick="window.location.reload()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Retry
                </button>
            </div>
        </div>
    </div>
</body>
</html>
