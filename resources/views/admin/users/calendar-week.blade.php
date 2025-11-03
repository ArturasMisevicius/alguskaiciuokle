<x-app-layout>
	<x-slot name="header">
		<div class="flex items-center justify-between">
			<div class="flex items-center gap-3">
				<i class="fas fa-calendar-week text-indigo-600"></i>
				<h2 class="font-semibold text-xl text-gray-800 leading-tight">
					{{ __('User Calendar — Week') }}
				</h2>
			</div>
			<a href="{{ route('admin.users') }}" class="btn-secondary">
				<i class="fas fa-arrow-left mr-2"></i>
				{{ __('Back to Users') }}
			</a>
		</div>
	</x-slot>

	@php
		$days = [];
		$cursor = $startOfWeek->copy();
		while ($cursor->lte($endOfWeek)) {
			$days[] = $cursor->copy();
			$cursor->addDay();
		}
	@endphp

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			@if(session('success'))
				<div class="alert-success mb-4"><i class="fas fa-check mr-2"></i>{{ session('success') }}</div>
			@endif
			<div class="card">
				<div class="card-header">
					<div class="flex items-center justify-between">
						<h3 class="text-lg font-semibold text-white">
							<i class="fas fa-user mr-2"></i>
							{{ $user->name }} — {{ $startOfWeek->format('M d') }} - {{ $endOfWeek->format('M d, Y') }}
						</h3>
						<div class="flex items-center gap-2">
							<a class="btn-secondary" href="{{ route('admin.users.calendar', ['user'=>$user->id,'view'=>'week','date'=>$prevWeek]) }}">
								<i class="fas fa-chevron-left mr-2"></i>{{ __('Prev') }}
							</a>
						<a class="btn-secondary" href="{{ route('admin.users.calendar', ['user'=>$user->id,'view'=>'week','date'=>$nextWeek]) }}">
								{{ __('Next') }}<i class="fas fa-chevron-right ml-2"></i>
							</a>
						<a class="btn-secondary" href="{{ route('admin.users.calendar', ['user'=>$user->id,'month'=>now()->month,'year'=>now()->year]) }}">
								<i class="far fa-calendar-alt mr-2"></i>{{ __('Month view') }}
							</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<form method="POST" action="{{ route('admin.users.calendar.save', $user) }}" class="space-y-6">
						@csrf
						<input type="hidden" name="month" value="{{ $anchorDay->month }}">
						<input type="hidden" name="year" value="{{ $anchorDay->year }}">

						<div class="grid grid-cols-7 gap-2">
							@foreach($days as $day)
								@php
									$key = $day->toDateString();
									$existing = $timesheets[$key] ?? null;
									$hoursPrefill = $existing ? round((float)($existing->calculated_hours ?? 0)) : null;
								@endphp
								<div class="border rounded p-2 bg-white">
									<div class="flex items-center justify-between mb-2">
										<div class="text-sm font-semibold text-gray-700">{{ $day->format('D d') }}</div>
										@if($existing && $hoursPrefill > 0)
											<span class="badge-info"><i class="far fa-clock mr-1"></i>{{ $hoursPrefill }}h</span>
										@endif
									</div>
								<label class="block text-xs text-gray-600 mb-1">{{ __('Hours') }}</label>
								<input name="hours[{{ $key }}]" type="number" step="1" min="0" max="24" value="{{ old('hours.'.$key, $hoursPrefill) }}" class="input w-full mb-2" placeholder="0">
								<label class="block text-xs text-gray-600 mb-1">{{ __('Tariff') }}</label>
								<select name="tariff[{{ $key }}]" class="input w-full">
									<option value="">{{ __('— Select tariff —') }}</option>
									@foreach($tariffs as $tariff)
										<option value="{{ $tariff->id }}">{{ $tariff->name }} ({{ number_format((float) $tariff->price_per_hour, 2) }})</option>
									@endforeach
								</select>
								</div>
							@endforeach
						</div>

						<div class="flex gap-3 pt-4">
							<button type="submit" class="btn-primary">
								<i class="fas fa-save mr-2"></i>{{ __('Save') }}
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</x-app-layout>


