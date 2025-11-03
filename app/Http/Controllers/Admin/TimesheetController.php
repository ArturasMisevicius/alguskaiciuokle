<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Timesheet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TimesheetController extends Controller
{
    /**
     * Display all timesheets with filtering.
     */
    public function index(Request $request): View
    {
        $query = Timesheet::with(['user', 'project', 'approver']);

        // Filter by status
        if ($request->has('status') && $request->get('status') !== 'all') {
            $query->where('status', $request->get('status'));
        }

        // Filter by user
        if ($request->has('user_id') && $request->get('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        // Filter by date range
        if ($request->has('date_from') && $request->get('date_from')) {
            $query->where('date', '>=', $request->get('date_from'));
        }
        if ($request->has('date_to') && $request->get('date_to')) {
            $query->where('date', '<=', $request->get('date_to'));
        }

        $timesheets = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get users for filter dropdown
        $users = User::whereHas('roles', function ($q) {
            $q->where('name', 'user');
        })->orderBy('name')->get();

        // Get pending count for badge
        $pendingCount = Timesheet::where('status', 'submitted')->count();

        return view('admin.timesheets.index', compact('timesheets', 'users', 'pendingCount'));
    }

    /**
     * Show details of a specific timesheet.
     */
    public function show(Timesheet $timesheet): View
    {
        $timesheet->load(['user', 'project', 'approver', 'pricingDetails.rateCard']);

        return view('admin.timesheets.show', compact('timesheet'));
    }

    /**
     * Approve a timesheet.
     */
    public function approve(Timesheet $timesheet)
    {
        if ($timesheet->status !== 'submitted') {
            return redirect()->route('admin.timesheets.index')
                ->with('error', 'Only submitted timesheets can be approved.');
        }

        $timesheet->approve(auth()->id());

        return redirect()->route('admin.timesheets.index')
            ->with('success', 'Timesheet approved successfully.');
    }

    /**
     * Reject a timesheet.
     */
    public function reject(Timesheet $timesheet)
    {
        if ($timesheet->status !== 'submitted') {
            return redirect()->route('admin.timesheets.index')
                ->with('error', 'Only submitted timesheets can be rejected.');
        }

        $timesheet->reject();

        return redirect()->route('admin.timesheets.index')
            ->with('success', 'Timesheet rejected.');
    }

    /**
     * Bulk approve timesheets.
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'timesheet_ids' => 'required|array',
            'timesheet_ids.*' => 'exists:timesheets,id',
        ]);

        $timesheets = Timesheet::whereIn('id', $validated['timesheet_ids'])
            ->where('status', 'submitted')
            ->get();

        foreach ($timesheets as $timesheet) {
            $timesheet->approve(auth()->id());
        }

        return redirect()->route('admin.timesheets.index')
            ->with('success', "Approved {$timesheets->count()} timesheets.");
    }
}
