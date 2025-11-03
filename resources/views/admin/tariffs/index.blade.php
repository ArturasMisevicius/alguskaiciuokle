<x-app-layout>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Tariffs') }}
    </h2>
</x-slot>

<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold">{{ __('Tariffs') }}</h1>
        <a href="{{ route('admin.tariffs.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            <i class="fas fa-plus mr-2"></i>{{ __('Create') }}
        </a>
    </div>

    <div class="bg-white shadow-sm sm:rounded-lg p-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Name') }}</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Start') }}</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('End') }}</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Price/hour') }}</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Active') }}</th>
                    <th class="px-3 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($tariffs as $tariff)
                    <tr>
                        <td class="px-3 py-2">{{ $tariff->name }}</td>
                        <td class="px-3 py-2">{{ \Illuminate\Support\Str::of($tariff->time_start)->limit(5, '') }}</td>
                        <td class="px-3 py-2">{{ \Illuminate\Support\Str::of($tariff->time_end)->limit(5, '') }}</td>
                        <td class="px-3 py-2">{{ number_format($tariff->price_per_hour, 2) }} €</td>
                        <td class="px-3 py-2">
                            @if($tariff->is_active)
                                <span class="text-green-600">{{ __('Yes') }}</span>
                            @else
                                <span class="text-gray-500">{{ __('No') }}</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-right space-x-2">
                            <a href="{{ route('admin.tariffs.edit', $tariff) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                            <form action="{{ route('admin.tariffs.destroy', $tariff) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('{{ __('Are you sure?') }}')">{{ __('Delete') }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-6 text-center text-gray-500">{{ __('No tariffs found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">{{ $tariffs->links() }}</div>
    </div>
</div>
</x-app-layout>


