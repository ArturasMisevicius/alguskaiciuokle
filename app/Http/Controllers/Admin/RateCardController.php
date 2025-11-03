<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\RateCard;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RateCardController extends Controller
{
    /**
     * Display a listing of rate cards.
     */
    public function index(): View
    {
        $rateCards = RateCard::with(['user', 'role', 'project'])
            ->orderBy('is_active', 'desc')
            ->orderBy('precedence', 'desc')
            ->paginate(20);

        return view('admin.rate-cards.index', compact('rateCards'));
    }

    /**
     * Show the form for creating a new rate card.
     */
    public function create(): View
    {
        $users = User::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $projects = Project::active()->orderBy('name')->get();

        return view('admin.rate-cards.create', compact('users', 'roles', 'projects'));
    }

    /**
     * Store a newly created rate card.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'role_id' => 'nullable|exists:roles,id',
            'project_id' => 'nullable|exists:projects,id',
            'days_of_week' => 'nullable|array',
            'days_of_week.*' => 'integer|min:1|max:7',
            'time_start' => 'nullable|date_format:H:i',
            'time_end' => 'nullable|date_format:H:i',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
            'rate_type' => 'required|in:fixed,multiplier',
            'rate_amount' => 'required_if:rate_type,fixed|nullable|numeric|min:0',
            'rate_multiplier' => 'required_if:rate_type,multiplier|nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'is_overtime' => 'boolean',
            'overtime_type' => 'nullable|in:daily,weekly',
            'overtime_threshold' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        // Convert days_of_week to JSON if provided
        if (isset($validated['days_of_week'])) {
            $validated['days_of_week'] = $validated['days_of_week'];
        }

        // Auto-calculate precedence will happen in the model

        RateCard::create($validated);

        return redirect()->route('admin.rate-cards.index')
            ->with('success', 'Rate card created successfully.');
    }

    /**
     * Show the form for editing a rate card.
     */
    public function edit(RateCard $rateCard): View
    {
        $users = User::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();
        $projects = Project::active()->orderBy('name')->get();

        return view('admin.rate-cards.edit', compact('rateCard', 'users', 'roles', 'projects'));
    }

    /**
     * Update the specified rate card.
     */
    public function update(Request $request, RateCard $rateCard)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'role_id' => 'nullable|exists:roles,id',
            'project_id' => 'nullable|exists:projects,id',
            'days_of_week' => 'nullable|array',
            'days_of_week.*' => 'integer|min:1|max:7',
            'time_start' => 'nullable|date_format:H:i',
            'time_end' => 'nullable|date_format:H:i',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date|after_or_equal:date_start',
            'rate_type' => 'required|in:fixed,multiplier',
            'rate_amount' => 'required_if:rate_type,fixed|nullable|numeric|min:0',
            'rate_multiplier' => 'required_if:rate_type,multiplier|nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
            'is_overtime' => 'boolean',
            'overtime_type' => 'nullable|in:daily,weekly',
            'overtime_threshold' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $rateCard->update($validated);

        return redirect()->route('admin.rate-cards.index')
            ->with('success', 'Rate card updated successfully.');
    }

    /**
     * Remove the specified rate card.
     */
    public function destroy(RateCard $rateCard)
    {
        $rateCard->delete();

        return redirect()->route('admin.rate-cards.index')
            ->with('success', 'Rate card deleted successfully.');
    }

    /**
     * Duplicate a rate card.
     */
    public function duplicate(RateCard $rateCard)
    {
        $newRateCard = $rateCard->replicate();
        $newRateCard->name = $rateCard->name . ' (Copy)';
        $newRateCard->save();

        return redirect()->route('admin.rate-cards.edit', $newRateCard)
            ->with('success', 'Rate card duplicated successfully.');
    }
}
