<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-clock text-indigo-600 mr-3"></i>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('My Timesheets') }}
                </h2>
            </div>
            <div class="flex items-center space-x-3">
                @if($runningTimer)
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium animate-pulse">
                        <i class="fas fa-circle text-green-500 mr-1"></i> Timer Running
                    </span>
                @endif
                <a href="{{ route('user.timesheets.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i> Add Entry
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Timer Card -->
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-stopwatch mr-2"></i>
                        Quick Timer
                    </h3>
                </div>
                <div class="card-body">
                    @if($runningTimer)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-lg font-semibold text-gray-900">Timer started at {{ $runningTimer->timer_started_at->format('H:i') }}</p>
                                <p class="text-sm text-gray-600">
                                    Project: {{ $runningTimer->project->name ?? 'No Project' }}
                                </p>
                                @if($runningTimer->note)
                                    <p class="text-sm text-gray-600 mt-1">Note: {{ $runningTimer->note }}</p>
                                @endif
                                <p class="text-xs text-gray-500 mt-2">
                                    Running for: <span id="timer-duration" class="font-semibold">Calculating...</span>
                                </p>
                            </div>
                            <form method="POST" action="{{ route('user.timesheets.timer.stop', $runningTimer) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-6 py-3 bg-red-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-red-700">
                                    <i class="fas fa-stop mr-2"></i> Stop Timer
                                </button>
                            </form>
                        </div>
                        <script>
                            const startTime = new Date('{{ $runningTimer->timer_started_at->toIso8601String() }}');
                            function updateTimer() {
                                const now = new Date();
                                const diff = now - startTime;
                                const hours = Math.floor(diff / 3600000);
                                const minutes = Math.floor((diff % 3600000) / 60000);
                                const seconds = Math.floor((diff % 60000) / 1000);
                                document.getElementById('timer-duration').textContent =
                                    `${hours}h ${minutes}m ${seconds}s`;
                            }
                            updateTimer();
                            setInterval(updateTimer, 1000);
                        </script>
                    @else
                        <form method="POST" action="{{ route('user.timesheets.timer.start') }}" class="flex items-end space-x-4">
                            @csrf
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Project (optional)</label>
                                <select name="project_id" class="w-full rounded-md border-gray-300">
                                    <option value="">No Project</option>
                                    @foreach(\App\Models\Project::active()->get() as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Note (optional)</label>
                                <input type="text" name="note" class="w-full rounded-md border-gray-300" placeholder="What are you working on?">
                            </div>
                            <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700">
                                <i class="fas fa-play mr-2"></i> Start Timer
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Week Navigation -->
            <div class="card mb-6">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('user.timesheets.index', ['week' => $weekStart->copy()->subWeek()->format('Y-m-d')]) }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md">
                            <i class="fas fa-chevron-left mr-2"></i> Previous Week
                        </a>

                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Week of {{ $weekStart->format('M d') }} - {{ $weekEnd->format('M d, Y') }}
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Total: <span class="font-semibold">{{ number_format($weeklyHours, 2) }} hours</span>
                                | Earned: <span class="font-semibold">€{{ number_format($weeklyAmount, 2) }}</span>
                            </p>
                        </div>

                        <a href="{{ route('user.timesheets.index', ['week' => $weekStart->copy()->addWeek()->format('Y-m-d')]) }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-md">
                            Next Week <i class="fas fa-chevron-right ml-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Timesheets Table -->
            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-list mr-2"></i>Time Entries
                    </h3>
                    <form method="POST" action="{{ route('user.timesheets.submit-week') }}">
                        @csrf
                        <input type="hidden" name="week_start" value="{{ $weekStart->format('Y-m-d') }}">
                        <button type="submit" class="text-white hover:text-gray-200">
                            <i class="fas fa-paper-plane mr-1"></i> Submit Week for Approval
                        </button>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($timesheets as $date => $dayTimesheets)
                                    @foreach($dayTimesheets as $timesheet)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ \Carbon\Carbon::parse($date)->format('D, M d') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $timesheet->project->name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ \Carbon\Carbon::parse($timesheet->start_time)->format('H:i') }} -
                                                {{ $timesheet->end_time ? \Carbon\Carbon::parse($timesheet->end_time)->format('H:i') : 'Running' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ number_format($timesheet->calculated_hours ?? 0, 2) }}h
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                €{{ number_format($timesheet->total_amount ?? 0, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($timesheet->status === 'draft')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Draft</span>
                                                @elseif($timesheet->status === 'submitted')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                                @elseif($timesheet->status === 'approved')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                                @if($timesheet->status === 'draft' && !$timesheet->timer_running)
                                                    <a href="{{ route('user.timesheets.edit', $timesheet) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('user.timesheets.submit', $timesheet) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-green-600 hover:text-green-900" title="Submit">
                                                            <i class="fas fa-paper-plane"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="{{ route('user.timesheets.destroy', $timesheet) }}" class="inline" onsubmit="return confirm('Are you sure?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-calendar-times text-4xl text-gray-300 mb-3"></i>
                                            <p class="text-lg">No time entries for this week</p>
                                            <p class="text-sm mt-2">Start tracking your time by adding an entry or starting a timer!</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
