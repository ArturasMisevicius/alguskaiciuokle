<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <i class="fas fa-plus text-indigo-600 mr-3"></i>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add Time Entry') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-clock mr-2"></i>Manual Time Entry
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.timesheets.store') }}">
                        @csrf

                        <!-- Date -->
                        <div class="mb-4">
                            <x-input-label for="date" :value="__('Date')" />
                            <input type="date" id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}"
                                   class="block mt-1 w-full rounded-md border-gray-300" required>
                            <x-input-error :messages="$errors->get('date')" class="mt-2" />
                        </div>

                        <!-- Project -->
                        <div class="mb-4">
                            <x-input-label for="project_id" :value="__('Project (optional)')" />
                            <select id="project_id" name="project_id" class="block mt-1 w-full rounded-md border-gray-300">
                                <option value="">Select a project...</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
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
                                <input type="time" id="start_time" name="start_time" value="{{ old('start_time', '09:00') }}"
                                       class="block mt-1 w-full rounded-md border-gray-300" required>
                                <x-input-error :messages="$errors->get('start_time')" class="mt-2" />
                            </div>

                            <!-- End Time -->
                            <div>
                                <x-input-label for="end_time" :value="__('End Time')" />
                                <input type="time" id="end_time" name="end_time" value="{{ old('end_time', '17:00') }}"
                                       class="block mt-1 w-full rounded-md border-gray-300" required>
                                <x-input-error :messages="$errors->get('end_time')" class="mt-2" />
                            </div>

                            <!-- Break Duration -->
                            <div>
                                <x-input-label for="break_duration" :value="__('Break (minutes)')" />
                                <input type="number" id="break_duration" name="break_duration" value="{{ old('break_duration', 0) }}"
                                       min="0" class="block mt-1 w-full rounded-md border-gray-300">
                                <x-input-error :messages="$errors->get('break_duration')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Note -->
                        <div class="mb-6">
                            <x-input-label for="note" :value="__('Note (optional)')" />
                            <textarea id="note" name="note" rows="3"
                                      class="block mt-1 w-full rounded-md border-gray-300"
                                      placeholder="Add any relevant notes about this time entry...">{{ old('note') }}</textarea>
                            <x-input-error :messages="$errors->get('note')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('user.timesheets.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400">
                                Cancel
                            </a>
                            <x-primary-button>
                                <i class="fas fa-save mr-2"></i>
                                {{ __('Save Entry') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quick Tips -->
            <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Quick Tips</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Your entry will be saved as a draft</li>
                                <li>You can edit drafts before submitting</li>
                                <li>Pricing will be calculated automatically based on your rate card</li>
                                <li>Submit your timesheet for approval when ready</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
