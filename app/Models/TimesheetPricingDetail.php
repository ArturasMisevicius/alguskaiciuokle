<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimesheetPricingDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'timesheet_id',
        'rate_card_id',
        'segment_date',
        'segment_start',
        'segment_end',
        'segment_hours',
        'rate_type',
        'applied_rate',
        'segment_amount',
        'currency',
        'is_overtime',
        'overtime_type',
        'calculation_metadata',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'segment_date' => 'date',
            'segment_hours' => 'decimal:2',
            'applied_rate' => 'decimal:2',
            'segment_amount' => 'decimal:2',
            'is_overtime' => 'boolean',
            'calculation_metadata' => 'array',
        ];
    }

    /**
     * Get the timesheet that owns the pricing detail.
     */
    public function timesheet(): BelongsTo
    {
        return $this->belongsTo(Timesheet::class);
    }

    /**
     * Get the rate card used for this pricing detail.
     */
    public function rateCard(): BelongsTo
    {
        return $this->belongsTo(RateCard::class);
    }
}
