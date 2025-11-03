<x-app-layout>
	<x-slot name="header">
		<div class="flex items-center justify-between">
			<div class="flex items-center gap-3">
				<i class="fas fa-calendar-alt text-indigo-600"></i>
				<h2 class="font-semibold text-xl text-gray-800 leading-tight">
					{{ __('User Calendar') }}
				</h2>
			</div>
			<a href="{{ route('admin.users') }}" class="btn-secondary">
				<i class="fas fa-arrow-left mr-2"></i>
				{{ __('Back to Users') }}
			</a>
		</div>
	</x-slot>

	@php
		$prev = $current->copy()->subMonth();
		$next = $current->copy()->addMonth();
		$cursor = $startOfCalendar->copy();
		$weeks = [];
		while ($cursor->lte($endOfCalendar)) {
			$week = [];
			for ($i = 0; $i < 7; $i++) {
				$week[] = $cursor->copy();
				$cursor->addDay();
			}
			$weeks[] = $week;
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
							{{ $user->name }} — {{ $current->format('F Y') }}
						</h3>
						<div class="flex items-center gap-2">
							<a class="btn-secondary" href="{{ route('admin.users.calendar', ['user'=>$user->id,'month'=>$prev->month,'year'=>$prev->year]) }}">
								<i class="fas fa-chevron-left mr-2"></i>{{ __('Prev') }}
							</a>
							<a class="btn-secondary" href="{{ route('admin.users.calendar', ['user'=>$user->id,'month'=>$next->month,'year'=>$next->year]) }}">
								{{ __('Next') }}<i class="fas fa-chevron-right ml-2"></i>
							</a>
						</div>
					</div>
				</div>
				<div class="card-body">
					<form method="POST" action="{{ route('admin.users.calendar.save', $user) }}" class="space-y-6">
						@csrf
						<input type="hidden" name="month" value="{{ $month }}">
						<input type="hidden" name="year" value="{{ $year }}">

						<div class="grid grid-cols-7 gap-2">
							@foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $dow)
								<div class="px-2 py-1 text-xs font-semibold text-gray-500 uppercase tracking-wide">{{ $dow }}</div>
							@endforeach

							@foreach($weeks as $week)
								@foreach($week as $day)
									@php
										$isCurrentMonth = $day->month === $current->month;
										$key = $day->toDateString();
										$existing = $timesheets[$key] ?? null;
										$hoursPrefill = $existing?->calculated_hours;
									@endphp
									<div class="border rounded p-2 bg-white {{ $isCurrentMonth ? '' : 'opacity-50' }}">
										<div class="flex items-center justify-between mb-2">
											<div class="text-sm font-semibold text-gray-700">{{ $day->day }}</div>
											@if($existing)
												<span class="badge-info"><i class="far fa-clock mr-1"></i>{{ number_format((float)($hoursPrefill ?? 0),2) }}h</span>
											@endif
										</div>
										@if($isCurrentMonth)
											<label class="block text-xs text-gray-600 mb-1">{{ __('Hours') }}</label>
											<input name="hours[{{ $key }}]" type="number" step="0.25" min="0" max="24" value="{{ old('hours.'.$key, $hoursPrefill) }}" class="input w-full" placeholder="0.0">
										@endif
									</div>
								@endforeach
							@endforeach
						</div>

						<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
							<div class="border rounded-lg p-4 bg-white md:col-span-2">
								<h4 class="font-semibold text-gray-800 mb-3">{{ __('Быстрые действия') }}</h4>
								<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
									<div>
										<label class="block text-sm text-gray-700 mb-1">{{ __('Дата') }}</label>
										<input type="date" name="quick[date]" value="{{ $current->isCurrentMonth() ? now()->toDateString() : $current->toDateString() }}" class="input w-full">
									</div>
									<div>
										<label class="block text-sm text-gray-700 mb-1">{{ __('Часы') }}</label>
										<input type="number" step="0.25" min="0" max="24" name="quick[hours]" class="input w-full" placeholder="0.0">
									</div>
									<div class="flex gap-3">
										<button type="submit" class="btn-primary">
											<i class="fas fa-plus mr-2"></i>{{ __('Добавить часы') }}
										</button>
										<button type="submit" class="btn-secondary">
											<i class="fas fa-save mr-2"></i>{{ __('Сохранить') }}
										</button>
									</div>
								</div>
							</div>
							<div class="border rounded-lg p-4 bg-white">
								<h4 class="font-semibold text-gray-800 mb-3">{{ __('Подсказки') }}</h4>
								<ul class="text-sm text-gray-600 list-disc pl-5 space-y-1">
									<li>{{ __('Навигация месяцами кнопками «Prev/Next».') }}</li>
									<li>{{ __('Вводите часы в ячейки дней текущего месяца.') }}</li>
									<li>{{ __('Пустое значение или 0 удаляет черновик записи за день.') }}</li>
								</ul>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</x-app-layout>
