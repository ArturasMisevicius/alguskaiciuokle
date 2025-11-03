<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <i class="fas fa-edit text-indigo-600 mr-3"></i>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Time Entry') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-clock mr-2"></i>Edit Time Entry
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.timesheets.update', $timesheet) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Date -->
                        <div class="mb-4">
                            <x-input-label for="date" :value="__('Date')" />
                            <input type="date" id="date" name="date" value="{{ old('date', $timesheet->date->format('Y-m-d')) }}"
                                   class="block mt-1 w-full rounded-md border-gray-300" required>
                            <x-input-error :messages="$errors->get('date')" class="mt-2" />
                        </div>

                        <!-- Project -->
                        <div class="mb-4">
                            <x-input-label for="project_id" :value="__('Project (optional)')" />
                            <select id="project_id" name="project_id" class="block mt-1 w-full rounded-md border-gray-300">
                                <option value="">Select a project...</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}"
                                            {{ old('project_id', $timesheet->project_id) == $project->id ? 'selected' : '' }}>
                                        {{ $project->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('project_id')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-4">
                            <!-- Start Time -->
                            <div>
                                <x-input-label for="start_time" :value="__('Start Time')" />
                                <input type="time" id="start_time" name="start_time"
                                       value="{{ old('start_time', substr($timesheet->start_time, 0, 5)) }}"
                                       class="block mt-1 w-full rounded-md border-gray-300" required>
                                <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                            </div>

                            <!-- End Time -->
                            <div>
                                <x-input-label for="end_time" :value="__('End Time')" />
                                <input type="time" id="end_time" name="end_time"
                                       value="{{ old('end_time', substr($timesheet->end_time, 0, 5)) }}"
                                       class="block mt-1 w-full rounded-md border-gray-300" required>
                                <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                            </div>

                            <!-- Break Duration -->
                            <div>
                                <x-input-label for="break_duration" :value="__('Break (minutes)')" />
                                <input type="number" id="break_duration" name="break_duration"
                                       value="{{ old('break_duration', $timesheet->break_duration) }}"
                                       min="0" class="block mt-1 w-full rounded-md border-gray-300">
                                <x-input-error :messages="$errors->get('break_duration')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Note -->
                        <div class="mb-6">
                            <x-input-label for="note" :value="__('Note (optional)')" />
                            <textarea id="note" name="note" rows="3"
                                      class="block mt-1 w-full rounded-md border-gray-300"
                                      placeholder="Add any relevant notes about this time entry...">{{ old('note', $timesheet->note) }}</textarea>
                            <x-input-error :messages="$errors->get('note')" class="mt-2" />
                        </div>

                        <!-- Current Calculation -->
                        @if($timesheet->calculated_hours)
                            <div class="mb-6 p-4 bg-gray-50 rounded-md">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2">Current Calculation</h4>
                                <div class="text-sm text-gray-600">
                                    <p>Hours: <span class="font-semibold">{{ number_format($timesheet->calculated_hours, 2) }}</span></p>
                                    <p>Amount: <span class="font-semibold">€{{ number_format($timesheet->total_amount ?? 0, 2) }}</span></p>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Will be recalculated when you save
                                </p>
                            </div>
                        @endif

                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('user.timesheets.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400">
                                Cancel
                            </a>
                            <x-primary-button>
                                <i class="fas fa-save mr-2"></i>
                                {{ __('Update Entry') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
