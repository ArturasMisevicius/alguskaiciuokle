<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-clock text-indigo-600 mr-3"></i>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Timesheets') }}</h2>
            </div>
            @isset($pendingCount)
                <span class="inline-flex items-center rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800">
                    {{ __('Pending:') }} {{ $pendingCount }}
                </span>
            @endisset
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg p-4 mb-6">
                <form method="GET" class="grid grid-flow-col auto-cols-fr gap-4 items-end overflow-x-auto">
                    <div class="min-w-[12rem]">
                        <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="all" {{ request('status','all')==='all' ? 'selected' : '' }}>{{ __('All') }}</option>
                            <option value="draft" {{ request('status')==='draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                            <option value="submitted" {{ request('status')==='submitted' ? 'selected' : '' }}>{{ __('Submitted') }}</option>
                            <option value="approved" {{ request('status')==='approved' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                            <option value="rejected" {{ request('status')==='rejected' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                        </select>
                    </div>
                    <div class="min-w-[12rem]">
                        <label for="user_id" class="block text-sm font-medium text-gray-700">{{ __('User') }}</label>
                        <select id="user_id" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">{{ __('All') }}</option>
                            @isset($users)
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ (string)request('user_id')===(string)$user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>
                    <div class="min-w-[12rem]">
                        <label for="date_from" class="block text-sm font-medium text-gray-700">{{ __('From') }}</label>
                        <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" placeholder="YYYY-MM-DD" inputmode="numeric" pattern="\d{4}-\d{2}-\d{2}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                    </div>
                    <div class="min-w-[12rem]">
                        <label for="date_to" class="block text-sm font-medium text-gray-700">{{ __('To') }}</label>
                        <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" placeholder="YYYY-MM-DD" inputmode="numeric" pattern="\d{4}-\d{2}-\d{2}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                    </div>
                    <div class="min-w-[8rem] flex items-end">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 w-full">
                            {{ __('Filter') }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-0 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('User') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Project') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Hours') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($timesheets as $timesheet)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $timesheet->date?->format('Y-m-d') ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $timesheet->user->name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $timesheet->project->name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format(($timesheet->calculated_hours ?? $timesheet->calculateHours() ?? 0), 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @class([
                                            'bg-gray-100 text-gray-800' => $timesheet->status==='draft',
                                            'bg-yellow-100 text-yellow-800' => $timesheet->status==='submitted',
                                            'bg-green-100 text-green-800' => $timesheet->status==='approved',
                                            'bg-red-100 text-red-800' => $timesheet->status==='rejected',
                                        ])">
                                        {{ ucfirst($timesheet->status ?? '-') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                    <a href="{{ route('admin.timesheets.show', $timesheet) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('View') }}</a>
                                    @if(($timesheet->status ?? null) === 'submitted')
                                        <form method="POST" action="{{ route('admin.timesheets.approve', $timesheet) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800">{{ __('Approve') }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.timesheets.reject', $timesheet) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-800">{{ __('Reject') }}</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500">{{ __('No timesheets found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                </div>

                <div class="px-4 py-3 border-t bg-gray-50">
                    {{ $timesheets->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


