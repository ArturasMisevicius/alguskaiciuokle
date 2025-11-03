# Tariffs (Тарифы)

This feature lets admins define hourly price bands by time of day. Timesheet salary is calculated by the matching time band when no specific Rate Card applies.

## Fields
- **Name**: Human-readable label.
- **Start time / End time**: Time band within a day (H:i).
- **Price per hour**: Monetary rate applied to hours in this band.
- **Active**: Whether this tariff is considered during calculation.

## Constraints & Validation
- `Start time` must be before `End time` (same-day band).
- Active tariffs cannot overlap: ranges `[start,end]` must not intersect.

## Usage
- Admin → Tariff: create one or more non-overlapping bands to cover the day, e.g.:
  - 00:00–08:00 (20.00)
  - 08:00–18:00 (15.00)
  - 18:00–22:00 (18.00)
  - 22:00–23:59 (20.00)
- During pricing, if no `RateCard` matches a timesheet segment, the engine uses the tariff that includes the segment start time.

## Notes
- For cross-midnight bands, create two bands (e.g., 22:00–23:59 and 00:00–08:00).
- Rate Cards still take precedence; tariffs are a fallback.

## Developer Pointers
- Model: `App\\Models\\Tariff`
- Controller: `App\\Http\\Controllers\\Admin\\TariffController`
- Views: `resources/views/admin/tariffs/*`
- Routes: `admin.tariffs.*`
- Pricing: `App\\Services\\PricingEngineService` fallback to tariff when no Rate Card.

## Archived show view (for reference)
```blade
<x-app-layout>
<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Tariff details') }}
    </h2>
</x-slot>

<div class="max-w-3xl mx-auto sm:px-6 lg:px-8 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold">{{ __('Tariff details') }}</h1>
        <a href="{{ route('admin.tariffs.edit', $tariff) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md">{{ __('Edit') }}</a>
    </div>

    <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
        <div>
            <div class="text-gray-500 text-sm">{{ __('Name') }}</div>
            <div class="text-gray-900">{{ $tariff->name }}</div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <div class="text-gray-500 text-sm">{{ __('Start time') }}</div>
                <div class="text-gray-900">{{ \Illuminate\Support\Str::of($tariff->time_start)->limit(5, '') }}</div>
            </div>
            <div>
                <div class="text-gray-500 text-sm">{{ __('End time') }}</div>
                <div class="text-gray-900">{{ \Illuminate\Support\Str::of($tariff->time_end)->limit(5, '') }}</div>
            </div>
        </div>
        <div>
            <div class="text-gray-500 text-sm">{{ __('Price per hour') }}</div>
            <div class="text-gray-900">{{ number_format($tariff->price_per_hour, 2) }} €</div>
        </div>
        <div>
            <div class="text-gray-500 text-sm">{{ __('Active') }}</div>
            <div class="text-gray-900">{{ $tariff->is_active ? __('Yes') : __('No') }}</div>
        </div>
        <div class="pt-4">
            <a href="{{ route('admin.tariffs.index') }}" class="px-4 py-2 bg-gray-100 rounded-md">{{ __('Back') }}</a>
        </div>
    </div>
</div>
</x-app-layout>
```


