<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tariff extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'time_start',
        'time_end',
        'price_per_hour',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_per_hour' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if the tariff applies to a given time (H:i:s).
     * Assumes same-day band where time_start <= time_end.
     */
    public function appliesToTime(string $time): bool
    {
        $start = $this->time_start ?? '00:00:00';
        $end = $this->time_end ?? '23:59:59';
        return $time >= $start && $time <= $end;
    }
}


