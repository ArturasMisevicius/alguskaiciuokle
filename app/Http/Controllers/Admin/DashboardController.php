<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $totalUsers = User::count();
        $totalAdmins = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->count();
        $totalRegularUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'user');
        })->count();

        return view('admin.dashboard', compact('totalUsers', 'totalAdmins', 'totalRegularUsers'));
    }

    /**
     * Display a listing of users.
     */
    public function users(): View
    {
        $users = User::with('roles')->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function createUser(): View
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user.
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,user',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'initial_password' => $validated['password'],
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing a user.
     */
    public function editUser(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required|in:admin,user',
            'new_password' => 'nullable|string|min:8',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        // Update password if provided
        if (! empty($validated['new_password'])) {
            $updateData['password'] = $validated['new_password'];
            $updateData['initial_password'] = $validated['new_password'];
        }

        $user->update($updateData);

        // Update role
        $user->roles()->detach();
        $user->assignRole($validated['role']);

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroyUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users')->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    /**
     * Display the calendar for a specific user.
     */
    public function userCalendar(Request $request, User $user): View
    {
        $view = (string) $request->query('view', 'month');

        if ($view === 'week') {
            $dateParam = $request->query('date');
            $anchorDay = $dateParam ? now()->parse($dateParam) : now();

            $startOfWeek = $anchorDay->copy()->startOfWeek();
            $endOfWeek = $anchorDay->copy()->endOfWeek();

            $timesheets = $user->timesheets()
                ->dateRange($startOfWeek->copy(), $endOfWeek->copy())
                ->get()
                ->keyBy(fn ($t) => $t->date->toDateString());

            $tariffs = \App\Models\Tariff::active()->orderBy('name')->get();

            return view('admin.users.calendar-week', [
                'user' => $user,
                'anchorDay' => $anchorDay,
                'startOfWeek' => $startOfWeek,
                'endOfWeek' => $endOfWeek,
                'prevWeek' => $startOfWeek->copy()->subWeek()->toDateString(),
                'nextWeek' => $startOfWeek->copy()->addWeek()->toDateString(),
                'timesheets' => $timesheets,
                'tariffs' => $tariffs,
            ]);
        }

        $month = (int) ($request->query('month', now()->month));
        $year = (int) ($request->query('year', now()->year));

        $current = now()->setYear($year)->setMonth($month)->startOfMonth();
        $startOfCalendar = $current->copy()->startOfWeek();
        $endOfCalendar = $current->copy()->endOfMonth()->endOfWeek();

        $timesheets = $user->timesheets()
            ->dateRange($current->copy()->startOfMonth(), $current->copy()->endOfMonth())
            ->get()
            ->keyBy(fn ($t) => $t->date->toDateString());

        $tariffs = \App\Models\Tariff::active()->orderBy('name')->get();

        return view('admin.users.calendar', [
            'user' => $user,
            'month' => $month,
            'year' => $year,
            'current' => $current,
            'startOfCalendar' => $startOfCalendar,
            'endOfCalendar' => $endOfCalendar,
            'timesheets' => $timesheets,
            'tariffs' => $tariffs,
        ]);
    }

    /**
     * Save calendar hours for the selected month.
     */
    public function saveUserCalendar(Request $request, User $user)
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'hours' => 'array',
            'hours.*' => 'nullable|numeric|min:0|max:24',
            'tariff' => 'array',
            'tariff.*' => 'nullable|integer|exists:tariffs,id',
            'quick.date' => 'nullable|date',
            'quick.hours' => 'nullable|numeric|min:0|max:24',
            'quick.tariff_id' => 'nullable|integer|exists:tariffs,id',
        ]);

        $month = (int) $validated['month'];
        $year = (int) $validated['year'];

        // Bulk month/week inputs
        $hoursByDate = $validated['hours'] ?? [];
        $tariffByDate = $validated['tariff'] ?? [];
        foreach ($hoursByDate as $date => $hours) {
            if ($hours === null || $hours === '' || (float) $hours <= 0) {
                // Remove draft entry if exists for that date
                $existingTs = $user->timesheets()->whereDate('date', $date)->first();
                if ($existingTs) {
                    $existingTs->pricingDetails()->delete();
                }
                $user->timesheets()->whereDate('date', $date)->draft()->delete();

                continue;
            }

            // Round to nearest hour
            $roundedHours = (int) round((float) $hours);

            $startTime = '09:00:00';
            $endTime = now()->setTime(9, 0, 0)->addHours($roundedHours)->format('H:i:s');

            $timesheet = $user->timesheets()->firstOrNew(['date' => $date]);
            $timesheet->fill([
                'project_id' => $timesheet->project_id ?? null,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'break_duration' => 0,
                'note' => $timesheet->note ?? null,
                'status' => 'draft',
                'currency' => $timesheet->currency ?? 'EUR',
            ])->save();

            // Apply selected tariff if provided
            $selectedTariffId = $tariffByDate[$date] ?? null;
            if ($selectedTariffId) {
                $tariff = \App\Models\Tariff::find($selectedTariffId);
                if ($tariff) {
                    // Reset existing pricing details
                    $timesheet->pricingDetails()->delete();

                    $segmentAmount = round($roundedHours * (float) $tariff->price_per_hour, 2);

                    \App\Models\TimesheetPricingDetail::create([
                        'timesheet_id' => $timesheet->id,
                        'rate_card_id' => null,
                        'segment_date' => $date,
                        'segment_start' => $startTime,
                        'segment_end' => $endTime,
                        'segment_hours' => $roundedHours,
                        'rate_type' => 'fixed',
                        'applied_rate' => $tariff->price_per_hour,
                        'segment_amount' => $segmentAmount,
                        'currency' => $timesheet->currency ?? 'EUR',
                        'is_overtime' => false,
                        'overtime_type' => null,
                        'calculation_metadata' => [
                            'tariff_id' => $tariff->id,
                            'tariff_name' => $tariff->name,
                        ],
                    ]);

                    $timesheet->update([
                        'calculated_hours' => $roundedHours,
                        'total_amount' => $segmentAmount,
                    ]);
                }
            }
        }

        // Quick add single entry
        if (! empty($validated['quick']['date']) && isset($validated['quick']['hours']) && (float) $validated['quick']['hours'] > 0) {
            $qDate = $validated['quick']['date'];
            // Round to nearest hour
            $qHours = (int) round((float) $validated['quick']['hours']);
            $startTime = '09:00:00';
            $endTime = now()->setTime(9, 0, 0)->addHours($qHours)->format('H:i:s');

            $timesheet = $user->timesheets()->firstOrNew(['date' => $qDate]);
            $timesheet->fill([
                'project_id' => $timesheet->project_id ?? null,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'break_duration' => 0,
                'note' => $timesheet->note ?? null,
                'status' => 'draft',
                'currency' => $timesheet->currency ?? 'EUR',
            ])->save();

            if (! empty($validated['quick']['tariff_id'])) {
                $tariff = \App\Models\Tariff::find((int) $validated['quick']['tariff_id']);
                if ($tariff) {
                    $timesheet->pricingDetails()->delete();

                    $segmentAmount = round($qHours * (float) $tariff->price_per_hour, 2);

                    \App\Models\TimesheetPricingDetail::create([
                        'timesheet_id' => $timesheet->id,
                        'rate_card_id' => null,
                        'segment_date' => $qDate,
                        'segment_start' => $startTime,
                        'segment_end' => $endTime,
                        'segment_hours' => $qHours,
                        'rate_type' => 'fixed',
                        'applied_rate' => $tariff->price_per_hour,
                        'segment_amount' => $segmentAmount,
                        'currency' => $timesheet->currency ?? 'EUR',
                        'is_overtime' => false,
                        'overtime_type' => null,
                        'calculation_metadata' => [
                            'tariff_id' => $tariff->id,
                            'tariff_name' => $tariff->name,
                        ],
                    ]);

                    $timesheet->update([
                        'calculated_hours' => $qHours,
                        'total_amount' => $segmentAmount,
                    ]);
                }
            }
        }

        return redirect()->route('admin.users.calendar', ['user' => $user->id, 'month' => $month, 'year' => $year])
            ->with('success', 'Calendar saved.');
    }
}
