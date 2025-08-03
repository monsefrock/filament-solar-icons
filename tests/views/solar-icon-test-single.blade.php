{{-- Single Icon Test View --}}
@try
    <x-icon :name="$iconName" class="w-full h-full" />
@catch(Exception $e)
    <div class="w-full h-full bg-red-100 border border-red-300 rounded flex items-center justify-center">
        <span class="text-red-500 text-xs">{{ $e->getMessage() }}</span>
    </div>
@endtry
