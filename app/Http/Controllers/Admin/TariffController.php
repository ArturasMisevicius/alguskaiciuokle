<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tariff;
use Illuminate\Http\Request;

class TariffController extends Controller
{
    public function index()
    {
        $tariffs = Tariff::orderBy('time_start')->paginate(15);

        return view('admin.tariffs.index', compact('tariffs'));
    }

    public function create()
    {
        return view('admin.tariffs.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        // Simple rule: ensure time_start < time_end
        if ($data['time_start'] >= $data['time_end']) {
            return back()->withErrors(['time_end' => __('End time must be after start time')])->withInput();
        }

        // Check overlapping with existing active tariffs
        if ($this->hasOverlap($data['time_start'].':00', $data['time_end'].':00')) {
            return back()->withErrors(['time_start' => __('Overlaps with an existing tariff time range')])->withInput();
        }

        Tariff::create($data);

        return redirect()->route('admin.tariffs.index')->with('status', __('Tariff created'));
    }

    public function edit(Tariff $tariff)
    {
        return view('admin.tariffs.edit', compact('tariff'));
    }

    public function show(Tariff $tariff)
    {
        return view('admin.tariffs.show', compact('tariff'));
    }

    public function update(Request $request, Tariff $tariff)
    {
        $data = $this->validateData($request);

        if ($data['time_start'] >= $data['time_end']) {
            return back()->withErrors(['time_end' => __('End time must be after start time')])->withInput();
        }

        if ($this->hasOverlap($data['time_start'].':00', $data['time_end'].':00', $tariff->id)) {
            return back()->withErrors(['time_start' => __('Overlaps with an existing tariff time range')])->withInput();
        }

        $tariff->update($data);

        return redirect()->route('admin.tariffs.index')->with('status', __('Tariff updated'));
    }

    public function destroy(Tariff $tariff)
    {
        $tariff->delete();

        return redirect()->route('admin.tariffs.index')->with('status', __('Tariff deleted'));
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'time_start' => ['required', 'date_format:H:i'],
            'time_end' => ['required', 'date_format:H:i'],
            'price_per_hour' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ], [], [
            'name' => __('Name'),
            'time_start' => __('Start time'),
            'time_end' => __('End time'),
            'price_per_hour' => __('Price per hour'),
        ]);
    }

    /**
     * Check if provided time band overlaps any existing active tariff.
     */
    protected function hasOverlap(string $start, string $end, ?int $ignoreId = null): bool
    {
        // Overlap condition for ranges [a,b] and [c,d]: a < d && c < b
        $query = Tariff::active();
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->where(function ($q) use ($start, $end) {
            $q->where('time_start', '<', $end)
                ->where('time_end', '>', $start);
        })->exists();
    }
}
