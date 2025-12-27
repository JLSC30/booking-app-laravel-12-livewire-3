<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\BranchSchedule;
use App\Models\Service;
use Carbon\Carbon;

class AppointmentSlotService
{
    public function getAvailableSlots(int $branchId, int $serviceId, string $date): array
    {
        try {
            $dateCarbon = Carbon::parse($date)->startOfDay();
        } catch (\Exception $e) {
            return [];
        }

        // Block same-day after 6 PM and past dates
        if ($dateCarbon->isToday() && now()->greaterThanOrEqualTo($dateCarbon->copy()->setTime(18, 0))) {
            return [];
        }

        if ($dateCarbon->isPast()) {
            return [];
        }

        $dayOfWeek = $dateCarbon->dayOfWeekIso;

        $schedule = BranchSchedule::where('branch_id', $branchId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_available', true)
            ->first();

        if (! $schedule) {
            return [];
        }

        $service = Service::findOrFail($serviceId);
        $serviceDuration = $service->duration;

        $hourlyLimit = (int) config('app.hourly_booking_limit', 1);

        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);

        $dayStart = $dateCarbon->copy()->setTime($startTime->hour, $startTime->minute);
        $dayEnd = $dateCarbon->copy()->setTime($endTime->hour, $endTime->minute);

        if ($dayEnd->lessThanOrEqualTo($dayStart)) {
            return [];
        }

        $slots = [];
        $current = $dayStart->copy(); // ← $current defined here

        while ($current->lessThan($dayEnd)) {
            $slotEnd = $current->copy()->addMinutes($serviceDuration);

            // Only add slot if it ends on or before closing time
            if ($slotEnd->lessThanOrEqualTo($dayEnd)) {
                // Count how many bookings already exist at this exact start time
                $bookingsAtThisTime = Appointment::where('branch_id', $branchId)
                    ->where('date', $dateCarbon->toDateString())
                    ->where('start_time', $current->format('H:i'))
                    ->where('status', 'confirmed')
                    ->count();

                // Add slot if under hourly limit
                if ($bookingsAtThisTime < $hourlyLimit) {
                    $slots[] = $current->format('H:i');
                }
            }

            // Move to next interval
            $current->addMinutes($schedule->slot_interval_minutes);
        }

        return $slots;
    }
}
