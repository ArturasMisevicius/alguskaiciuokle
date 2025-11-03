<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Timesheet extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'project_id',
        'date',
        'start_time',
        'end_time',
        'break_duration',
        'note',
        'status',
        'submitted_at',
        'approved_at',
        'approved_by',
        'timer_started_at',
        'timer_running',
        'calculated_hours',
        'total_amount',
        'currency',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'timer_started_at' => 'datetime',
            'timer_running' => 'boolean',
            'calculated_hours' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * Get the user that owns the timesheet.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the project associated with the timesheet.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the approver user.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the pricing details for the timesheet.
     */
    public function pricingDetails(): HasMany
    {
        return $this->hasMany(TimesheetPricingDetail::class);
    }

    /**
     * Scope a query to only include draft timesheets.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to only include submitted timesheets.
     */
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    /**
     * Scope a query to only include approved timesheets.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope a query to only include running timers.
     */
    public function scopeTimerRunning($query)
    {
        return $query->where('timer_running', true);
    }

    /**
     * Scope a query to filter by user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Calculate total hours (end_time - start_time - break_duration).
     */
    public function calculateHours(): float
    {
        if (! $this->start_time || ! $this->end_time) {
            return 0;
        }

        // $this->date may already be a Carbon instance (cast as 'date') or a full datetime string.
        // Build the datetime safely to avoid double time specification errors.
        $date = \Carbon\Carbon::parse($this->date);
        $start = $date->copy()->setTimeFromTimeString((string) $this->start_time);
        $end = $date->copy()->setTimeFromTimeString((string) $this->end_time);

        // Handle overnight shifts
        if ($end->lt($start)) {
            $end->addDay();
        }

        $totalMinutes = $end->diffInMinutes($start);
        $workMinutes = $totalMinutes - $this->break_duration;

        return round($workMinutes / 60, 2);
    }

    /**
     * Submit the timesheet for approval.
     */
    public function submit(): void
    {
        $this->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    /**
     * Approve the timesheet.
     */
    public function approve($approverId): void
    {
        $this->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => $approverId,
        ]);
    }

    /**
     * Reject the timesheet.
     */
    public function reject(): void
    {
        $this->update([
            'status' => 'rejected',
        ]);
    }

    /**
     * Start the timer.
     */
    public function startTimer(): void
    {
        $this->update([
            'timer_started_at' => now(),
            'timer_running' => true,
        ]);
    }

    /**
     * Stop the timer and calculate end_time.
     */
    public function stopTimer(): void
    {
        if ($this->timer_running && $this->timer_started_at) {
            $now = now();
            $this->update([
                'end_time' => $now->format('H:i:s'),
                'timer_running' => false,
            ]);
        }
    }
}
