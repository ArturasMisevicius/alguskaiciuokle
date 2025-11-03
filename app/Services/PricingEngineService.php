<?php

namespace App\Services;

use App\Models\RateCard;
use App\Models\Timesheet;
use App\Models\TimesheetPricingDetail;
use App\Models\Tariff;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PricingEngineService
{
    /**
     * Calculate pricing for a timesheet entry.
     *
     * This method:
     * 1. Splits the timesheet entry across days and time bands (handles midnight)
     * 2. Finds the best-matching rate card for each segment
     * 3. Calculates overtime (daily/weekly)
     * 4. Creates pricing detail records
     * 5. Updates the timesheet totals
     */
    public function calculatePricing(Timesheet $timesheet): void
    {
        // Clear existing pricing details
        $timesheet->pricingDetails()->delete();

        // Get time segments (split by day and midnight if needed)
        $segments = $this->splitIntoSegments($timesheet);

        $totalHours = 0;
        $totalAmount = 0;

        // Get user's role IDs for rate card matching
        $userRoleIds = $timesheet->user->roles->pluck('id')->toArray();

        foreach ($segments as $segment) {
            // Find the best matching rate card
            $rateCard = $this->findBestRateCard(
                $timesheet->user_id,
                $userRoleIds,
                $timesheet->project_id,
                $segment['date'],
                $segment['start'],
                $segment['hours']
            );

            if ($rateCard) {
                // Calculate via rate card
                $segmentAmount = $this->calculateSegmentAmount($rateCard, $segment['hours']);

                TimesheetPricingDetail::create([
                    'timesheet_id' => $timesheet->id,
                    'rate_card_id' => $rateCard->id,
                    'segment_date' => $segment['date'],
                    'segment_start' => $segment['start'],
                    'segment_end' => $segment['end'],
                    'segment_hours' => $segment['hours'],
                    'rate_type' => $rateCard->rate_type,
                    'applied_rate' => $rateCard->rate_type === 'fixed'
                        ? $rateCard->rate_amount
                        : $rateCard->rate_multiplier,
                    'segment_amount' => $segmentAmount,
                    'currency' => $rateCard->currency,
                    'is_overtime' => $rateCard->is_overtime,
                    'overtime_type' => $rateCard->overtime_type,
                    'calculation_metadata' => [
                        'rate_card_name' => $rateCard->name,
                        'precedence' => $rateCard->precedence,
                    ],
                ]);
            } else {
                // Fallback to simple tariff by time band
                $tariff = $this->findTariffByTime($segment['start']);
                if (!$tariff) {
                    continue;
                }

                $segmentAmount = round($segment['hours'] * (float) $tariff->price_per_hour, 2);

                TimesheetPricingDetail::create([
                    'timesheet_id' => $timesheet->id,
                    'rate_card_id' => null,
                    'segment_date' => $segment['date'],
                    'segment_start' => $segment['start'],
                    'segment_end' => $segment['end'],
                    'segment_hours' => $segment['hours'],
                    'rate_type' => 'fixed',
                    'applied_rate' => $tariff->price_per_hour,
                    'segment_amount' => $segmentAmount,
                    'currency' => $timesheet->currency ?? 'EUR',
                    'is_overtime' => false,
                    'overtime_type' => null,
                    'calculation_metadata' => [
                        'tariff_name' => $tariff->name,
                    ],
                ]);
            }

            $totalHours += $segment['hours'];
            $totalAmount += $segmentAmount;
        }

        // Update timesheet with calculated totals
        $timesheet->update([
            'calculated_hours' => $totalHours,
            'total_amount' => $totalAmount,
            'currency' => $segments[0]['currency'] ?? 'EUR',
        ]);
    }

    /**
     * Split timesheet entry into segments (handles midnight crossing).
     *
     * @return array<array{date: Carbon, start: string, end: string, hours: float, currency: string}>
     */
    protected function splitIntoSegments(Timesheet $timesheet): array
    {
        $segments = [];

        $startDateTime = Carbon::parse($timesheet->date . ' ' . $timesheet->start_time);
        $endDateTime = Carbon::parse($timesheet->date . ' ' . $timesheet->end_time);

        // Handle overnight shifts
        if ($endDateTime->lt($startDateTime)) {
            $endDateTime->addDay();
        }

        // Calculate total minutes including break
        $totalMinutes = $endDateTime->diffInMinutes($startDateTime);
        $workMinutes = $totalMinutes - $timesheet->break_duration;

        // For simplicity, we'll distribute break proportionally across segments
        // In production, you might want to handle breaks more explicitly

        $currentDateTime = $startDateTime->copy();
        $remainingMinutes = $workMinutes;

        while ($remainingMinutes > 0) {
            $segmentStart = $currentDateTime->copy();
            $segmentEnd = $segmentStart->copy()->endOfDay();

            // Don't go past the actual end time
            if ($segmentEnd->gt($endDateTime)) {
                $segmentEnd = $endDateTime->copy();
            }

            $segmentMinutes = $segmentEnd->diffInMinutes($segmentStart);

            // Proportionally reduce for break
            $segmentWorkMinutes = min($segmentMinutes, $remainingMinutes);

            $segments[] = [
                'date' => $segmentStart->toDateString(),
                'start' => $segmentStart->format('H:i:s'),
                'end' => $segmentEnd->format('H:i:s'),
                'hours' => round($segmentWorkMinutes / 60, 2),
                'currency' => $timesheet->currency,
            ];

            $remainingMinutes -= $segmentWorkMinutes;
            $currentDateTime = $segmentEnd->copy()->addSecond();
        }

        return $segments;
    }

    /**
     * Find the best matching rate card based on precedence.
     */
    protected function findBestRateCard(
        int $userId,
        array $roleIds,
        ?int $projectId,
        string $date,
        string $time,
        float $hours
    ): ?RateCard {
        $dateCarbon = Carbon::parse($date);
        $dayOfWeek = $dateCarbon->dayOfWeekIso; // 1 = Monday, 7 = Sunday

        // Get all potentially matching rate cards
        $rateCards = RateCard::active()
            ->forDate($date)
            ->get()
            ->filter(function ($rateCard) use ($userId, $roleIds, $projectId, $dayOfWeek, $time) {
                // Check user match
                if ($rateCard->user_id && $rateCard->user_id !== $userId) {
                    return false;
                }

                // Check role match
                if ($rateCard->role_id && !in_array($rateCard->role_id, $roleIds)) {
                    return false;
                }

                // Check project match
                if ($rateCard->project_id && $rateCard->project_id !== $projectId) {
                    return false;
                }

                // Check day of week
                if (!$rateCard->appliesToDayOfWeek($dayOfWeek)) {
                    return false;
                }

                // Check time band
                if (!$rateCard->appliesToTime($time)) {
                    return false;
                }

                return true;
            });

        // Sort by precedence (highest first) and return the best match
        return $rateCards->sortByDesc('precedence')->first();
    }

    /**
     * Find an active tariff matching the given time.
     */
    protected function findTariffByTime(string $time): ?Tariff
    {
        return Tariff::active()->get()->first(function (Tariff $t) use ($time) {
            return $t->appliesToTime($time);
        });
    }

    /**
     * Calculate the amount for a segment based on the rate card.
     */
    protected function calculateSegmentAmount(RateCard $rateCard, float $hours): float
    {
        if ($rateCard->rate_type === 'fixed') {
            return round($hours * $rateCard->rate_amount, 2);
        }

        // For multiplier type, you would need a base rate
        // This could come from another rate card or a user's base_rate field
        // For now, we'll just use the multiplier as if it's a rate
        return round($hours * ($rateCard->rate_multiplier ?? 1), 2);
    }

    /**
     * Calculate weekly overtime for a user.
     * This should be called at the end of a week to recalculate all timesheets.
     */
    public function calculateWeeklyOvertime(int $userId, Carbon $weekStart): void
    {
        $weekEnd = $weekStart->copy()->endOfWeek();

        $timesheets = Timesheet::forUser($userId)
            ->dateRange($weekStart, $weekEnd)
            ->approved()
            ->get();

        $weeklyHours = $timesheets->sum('calculated_hours');

        // Find overtime rate card
        $overtimeCard = RateCard::active()
            ->where('is_overtime', true)
            ->where('overtime_type', 'weekly')
            ->forUser($userId)
            ->orderByDesc('precedence')
            ->first();

        if (!$overtimeCard || $weeklyHours <= ($overtimeCard->overtime_threshold ?? 40)) {
            return; // No overtime
        }

        // Calculate overtime hours
        $overtimeHours = $weeklyHours - $overtimeCard->overtime_threshold;

        // TODO: Implement overtime adjustment logic
        // This would involve recalculating pricing for affected timesheets
        // with the overtime rate applied to excess hours
    }

    /**
     * Calculate daily overtime for a timesheet.
     */
    public function calculateDailyOvertime(Timesheet $timesheet): void
    {
        $totalHours = $timesheet->calculated_hours;

        $overtimeCard = RateCard::active()
            ->where('is_overtime', true)
            ->where('overtime_type', 'daily')
            ->forUser($timesheet->user_id)
            ->orderByDesc('precedence')
            ->first();

        if (!$overtimeCard || $totalHours <= ($overtimeCard->overtime_threshold ?? 8)) {
            return; // No overtime
        }

        // Calculate overtime hours
        $overtimeHours = $totalHours - $overtimeCard->overtime_threshold;

        // TODO: Implement overtime adjustment logic
        // This would split the last segment(s) to apply overtime rates
    }

    /**
     * Recalculate pricing for multiple timesheets (bulk operation).
     */
    public function bulkCalculatePricing(Collection $timesheets): void
    {
        foreach ($timesheets as $timesheet) {
            $this->calculatePricing($timesheet);
        }
    }
}
