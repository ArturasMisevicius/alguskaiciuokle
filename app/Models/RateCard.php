<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RateCard extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'user_id',
        'role_id',
        'project_id',
        'days_of_week',
        'time_start',
        'time_end',
        'date_start',
        'date_end',
        'rate_type',
        'rate_amount',
        'rate_multiplier',
        'currency',
        'precedence',
        'is_overtime',
        'overtime_type',
        'overtime_threshold',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'days_of_week' => 'array',
            'date_start' => 'date',
            'date_end' => 'date',
            'rate_amount' => 'decimal:2',
            'rate_multiplier' => 'decimal:2',
            'overtime_threshold' => 'decimal:2',
            'is_overtime' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user associated with the rate card.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the role associated with the rate card.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the project associated with the rate card.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the pricing details using this rate card.
     */
    public function pricingDetails(): HasMany
    {
        return $this->hasMany(TimesheetPricingDetail::class);
    }

    /**
     * Scope a query to only include active rate cards.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->whereNull('user_id')->orWhere('user_id', $userId);
        });
    }

    /**
     * Scope a query to filter by role.
     */
    public function scopeForRole($query, $roleId)
    {
        return $query->where(function ($q) use ($roleId) {
            $q->whereNull('role_id')->orWhere('role_id', $roleId);
        });
    }

    /**
     * Scope a query to filter by project.
     */
    public function scopeForProject($query, $projectId)
    {
        return $query->where(function ($q) use ($projectId) {
            $q->whereNull('project_id')->orWhere('project_id', $projectId);
        });
    }

    /**
     * Scope a query to filter by date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->where(function ($q) use ($date) {
            $q->where(function ($subQ) use ($date) {
                $subQ->whereNull('date_start')->orWhere('date_start', '<=', $date);
            })->where(function ($subQ) use ($date) {
                $subQ->whereNull('date_end')->orWhere('date_end', '>=', $date);
            });
        });
    }

    /**
     * Check if this rate card applies to a given day of week.
     */
    public function appliesToDayOfWeek(int $dayOfWeek): bool
    {
        if (empty($this->days_of_week)) {
            return true; // Applies to all days
        }

        return in_array($dayOfWeek, $this->days_of_week);
    }

    /**
     * Check if this rate card applies to a given time.
     */
    public function appliesToTime(string $time): bool
    {
        $timeStart = $this->time_start ?? '00:00:00';
        $timeEnd = $this->time_end ?? '23:59:59';

        return $time >= $timeStart && $time <= $timeEnd;
    }

    /**
     * Calculate the precedence score automatically based on specificity.
     * More specific rules get higher scores.
     */
    public function calculatePrecedence(): int
    {
        $score = 0;

        // User-specific: +100
        if ($this->user_id) {
            $score += 100;
        }

        // Role-specific: +50
        if ($this->role_id) {
            $score += 50;
        }

        // Project-specific: +30
        if ($this->project_id) {
            $score += 30;
        }

        // Day-specific: +10
        if (! empty($this->days_of_week)) {
            $score += 10;
        }

        // Time-band specific: +10
        if ($this->time_start || $this->time_end) {
            $score += 10;
        }

        // Date-range specific: +5
        if ($this->date_start || $this->date_end) {
            $score += 5;
        }

        return $score;
    }

    /**
     * Boot the model and auto-calculate precedence.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($rateCard) {
            if ($rateCard->precedence === 0 || $rateCard->precedence === null) {
                $rateCard->precedence = $rateCard->calculatePrecedence();
            }
        });
    }
}
