<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Timesheet;
use App\Services\PricingEngineService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimesheetController extends Controller
{
    protected PricingEngineService $pricingEngine;

    public function __construct(PricingEngineService $pricingEngine)
    {
        $this->pricingEngine = $pricingEngine;
    }

    /**
     * Display timesheets with weekly view.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Get current week or specified week
        $weekStart = $request->has('week')
            ? Carbon::parse($request->get('week'))->startOfWeek()
            : Carbon::now()->startOfWeek();

        $weekEnd = $weekStart->copy()->endOfWeek();

        // Get timesheets for the week
        $timesheets = Timesheet::forUser($user->id)
            ->dateRange($weekStart, $weekEnd)
            ->with(['project', 'pricingDetails'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get()
            ->groupBy(function ($timesheet) {
                return $timesheet->date->format('Y-m-d');
            });

        // Calculate weekly totals
        $weeklyHours = Timesheet::forUser($user->id)
            ->dateRange($weekStart, $weekEnd)
            ->sum('calculated_hours');

        $weeklyAmount = Timesheet::forUser($user->id)
            ->dateRange($weekStart, $weekEnd)
            ->sum('total_amount');

        // Get running timer if any
        $runningTimer = Timesheet::forUser($user->id)
            ->timerRunning()
            ->first();

        return view('user.timesheets.index', compact(
            'timesheets',
            'weekStart',
            'weekEnd',
            'weeklyHours',
            'weeklyAmount',
            'runningTimer'
        ));
    }

    /**
     * Show the form for creating a new timesheet.
     */
    public function create(): View
    {
        $projects = Project::active()->get();

        return view('user.timesheets.create', compact('projects'));
    }

    /**
     * Store a newly created timesheet.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'break_duration' => 'nullable|integer|min:0',
            'note' => 'nullable|string|max:1000',
        ]);

        $timesheet = Timesheet::create([
            'user_id' => auth()->id(),
            'project_id' => $validated['project_id'] ?? null,
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'break_duration' => $validated['break_duration'] ?? 0,
            'note' => $validated['note'] ?? null,
            'status' => 'draft',
        ]);

        // Calculate hours
        $hours = $timesheet->calculateHours();
        $timesheet->update(['calculated_hours' => $hours]);

        // Calculate pricing
        $this->pricingEngine->calculatePricing($timesheet);

        return redirect()->route('user.timesheets.index')
            ->with('success', 'Timesheet entry created successfully.');
    }

    /**
     * Show the form for editing a timesheet.
     */
    public function edit(Timesheet $timesheet): View
    {
        // Ensure user owns this timesheet
        if ($timesheet->user_id !== auth()->id()) {
            abort(403);
        }

        // Can only edit draft timesheets
        if ($timesheet->status !== 'draft') {
            return redirect()->route('user.timesheets.index')
                ->with('error', 'Cannot edit submitted or approved timesheets.');
        }

        $projects = Project::active()->get();

        return view('user.timesheets.edit', compact('timesheet', 'projects'));
    }

    /**
     * Update the specified timesheet.
     */
    public function update(Request $request, Timesheet $timesheet)
    {
        // Ensure user owns this timesheet
        if ($timesheet->user_id !== auth()->id()) {
            abort(403);
        }

        // Can only edit draft timesheets
        if ($timesheet->status !== 'draft') {
            return redirect()->route('user.timesheets.index')
                ->with('error', 'Cannot edit submitted or approved timesheets.');
        }

        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'break_duration' => 'nullable|integer|min:0',
            'note' => 'nullable|string|max:1000',
        ]);

        $timesheet->update([
            'project_id' => $validated['project_id'] ?? null,
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'break_duration' => $validated['break_duration'] ?? 0,
            'note' => $validated['note'] ?? null,
        ]);

        // Recalculate hours and pricing
        $hours = $timesheet->calculateHours();
        $timesheet->update(['calculated_hours' => $hours]);
        $this->pricingEngine->calculatePricing($timesheet);

        return redirect()->route('user.timesheets.index')
            ->with('success', 'Timesheet updated successfully.');
    }

    /**
     * Delete a draft timesheet.
     */
    public function destroy(Timesheet $timesheet)
    {
        // Ensure user owns this timesheet
        if ($timesheet->user_id !== auth()->id()) {
            abort(403);
        }

        // Can only delete draft timesheets
        if ($timesheet->status !== 'draft') {
            return redirect()->route('user.timesheets.index')
                ->with('error', 'Cannot delete submitted or approved timesheets.');
        }

        $timesheet->delete();

        return redirect()->route('user.timesheets.index')
            ->with('success', 'Timesheet deleted successfully.');
    }

    /**
     * Submit timesheet for approval.
     */
    public function submit(Timesheet $timesheet)
    {
        // Ensure user owns this timesheet
        if ($timesheet->user_id !== auth()->id()) {
            abort(403);
        }

        if ($timesheet->status !== 'draft') {
            return redirect()->route('user.timesheets.index')
                ->with('error', 'Only draft timesheets can be submitted.');
        }

        $timesheet->submit();

        return redirect()->route('user.timesheets.index')
            ->with('success', 'Timesheet submitted for approval.');
    }

    /**
     * Submit all draft timesheets for the week.
     */
    public function submitWeek(Request $request)
    {
        $weekStart = Carbon::parse($request->get('week_start'))->startOfWeek();
        $weekEnd = $weekStart->copy()->endOfWeek();

        $drafts = Timesheet::forUser(auth()->id())
            ->dateRange($weekStart, $weekEnd)
            ->draft()
            ->get();

        foreach ($drafts as $timesheet) {
            $timesheet->submit();
        }

        return redirect()->route('user.timesheets.index')
            ->with('success', "Submitted {$drafts->count()} timesheets for approval.");
    }

    /**
     * Start a timer for a new timesheet.
     */
    public function startTimer(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'nullable|exists:projects,id',
            'note' => 'nullable|string|max:1000',
        ]);

        // Check if user already has a running timer
        $existingTimer = Timesheet::forUser(auth()->id())
            ->timerRunning()
            ->first();

        if ($existingTimer) {
            return redirect()->route('user.timesheets.index')
                ->with('error', 'You already have a running timer. Stop it before starting a new one.');
        }

        $timesheet = Timesheet::create([
            'user_id' => auth()->id(),
            'project_id' => $validated['project_id'] ?? null,
            'date' => now()->toDateString(),
            'start_time' => now()->format('H:i:s'),
            'note' => $validated['note'] ?? null,
            'status' => 'draft',
        ]);

        $timesheet->startTimer();

        return redirect()->route('user.timesheets.index')
            ->with('success', 'Timer started successfully.');
    }

    /**
     * Stop a running timer.
     */
    public function stopTimer(Timesheet $timesheet)
    {
        // Ensure user owns this timesheet
        if ($timesheet->user_id !== auth()->id()) {
            abort(403);
        }

        if (! $timesheet->timer_running) {
            return redirect()->route('user.timesheets.index')
                ->with('error', 'This timer is not running.');
        }

        $timesheet->stopTimer();

        // Calculate hours and pricing
        $hours = $timesheet->calculateHours();
        $timesheet->update(['calculated_hours' => $hours]);
        $this->pricingEngine->calculatePricing($timesheet);

        return redirect()->route('user.timesheets.index')
            ->with('success', 'Timer stopped successfully.');
    }
}
