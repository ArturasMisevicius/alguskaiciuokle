<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <i class="fas fa-dollar-sign text-indigo-600 mr-3"></i>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Rate Cards') }}
                </h2>
            </div>
            <a href="{{ route('admin.rate-cards.create') }}" class="btn-success">
                <i class="fas fa-plus mr-2"></i>
                New Rate Card
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="alert-success" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-error" role="alert">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-list mr-2"></i>
                        All Rate Cards
                    </h3>
                </div>
                <div class="card-body">
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead class="table-header">
                                <tr>
                                    <th class="table-header-cell">Name</th>
                                    <th class="table-header-cell">User</th>
                                    <th class="table-header-cell">Role</th>
                                    <th class="table-header-cell">Project</th>
                                    <th class="table-header-cell">Type</th>
                                    <th class="table-header-cell">Amount</th>
                                    <th class="table-header-cell">Currency</th>
                                    <th class="table-header-cell">Active</th>
                                    <th class="table-header-cell">Precedence</th>
                                    <th class="table-header-cell">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-body">
                                @forelse($rateCards as $rateCard)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="table-cell font-medium text-gray-900">{{ $rateCard->name }}</td>
                                        <td class="table-cell">{{ optional($rateCard->user)->name ?? '—' }}</td>
                                        <td class="table-cell">{{ optional($rateCard->role)->name ?? '—' }}</td>
                                        <td class="table-cell">{{ optional($rateCard->project)->name ?? '—' }}</td>
                                        <td class="table-cell">
                                            <span class="badge-info">
                                                {{ ucfirst($rateCard->rate_type) }}
                                            </span>
                                        </td>
                                        <td class="table-cell">
                                            @if($rateCard->rate_type === 'fixed')
                                                {{ number_format((float)($rateCard->rate_amount ?? 0), 2) }}@if(($rateCard->currency ?? 'EUR') === 'EUR') €@endif
                                            @else
                                                ×{{ rtrim(rtrim(number_format((float)($rateCard->rate_multiplier ?? 0), 4, '.', ''), '0'), '.') }}
                                            @endif
                                        </td>
                                        <td class="table-cell">{{ $rateCard->currency }}</td>
                                        <td class="table-cell">
                                            @if($rateCard->is_active)
                                                <span class="badge-success"><i class="fas fa-check mr-1"></i>Active</span>
                                            @else
                                                <span class="badge-error"><i class="fas fa-times mr-1"></i>Inactive</span>
                                            @endif
                                        </td>
                                        <td class="table-cell">{{ $rateCard->precedence }}</td>
                                        <td class="table-cell">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('admin.rate-cards.edit', $rateCard) }}" class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700 transition">
                                                    <i class="fas fa-edit mr-1"></i>
                                                    Edit
                                                </a>
                                                <form action="{{ route('admin.rate-cards.duplicate', $rateCard) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-3 py-1 bg-amber-500 text-white text-xs rounded hover:bg-amber-600 transition">
                                                        <i class="fas fa-copy mr-1"></i>
                                                        Duplicate
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.rate-cards.destroy', $rateCard) }}" method="POST" class="inline" onsubmit="return confirm('Delete this rate card?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition">
                                                        <i class="fas fa-trash mr-1"></i>
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-file-invoice-dollar text-4xl mb-2"></i>
                                            <p>No rate cards found.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $rateCards->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


