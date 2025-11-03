<x-app-layout>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Create Tariff') }}
    </h2>
</x-slot>

<div class="max-w-3xl mx-auto sm:px-6 lg:px-8 py-6">

    <div class="bg-white shadow-sm sm:rounded-lg p-6">
        <form method="POST" action="{{ route('admin.tariffs.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('Start time') }}</label>
                    <input type="time" name="time_start" value="{{ old('time_start') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('time_start')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">{{ __('End time') }}</label>
                    <input type="time" name="time_end" value="{{ old('time_end') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('time_end')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">{{ __('Price per hour') }}</label>
                <input type="number" step="0.01" min="0" name="price_per_hour" value="{{ old('price_per_hour') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @error('price_per_hour')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>

            <div class="flex items-center">
                <input type="hidden" name="is_active" value="0">
                <input id="is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', true)) class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-700">{{ __('Active') }}</label>
            </div>

            <div class="pt-4 flex items-center justify-end space-x-2">
                <a href="{{ route('admin.tariffs.index') }}" class="px-4 py-2 bg-gray-100 rounded-md">{{ __('Cancel') }}</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">{{ __('Save') }}</button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>


