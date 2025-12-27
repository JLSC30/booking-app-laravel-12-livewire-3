<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateAppointmentStatuses extends Command
{
    protected $signature = 'appointments:update-statuses';

    protected $description = 'Automatically mark confirmed appointments as completed if their date has passed, and clear the cancellation token';

    public function handle()
    {
        $now = Carbon::now();
        $today = Carbon::today();

        // Use dedicated logger
        $logger = Log::channel('appointment_status');

        $logger->info('=== Appointment Status Update Started ===');
        $logger->info("Run at: {$now->toDateTimeString()}");
        $logger->info("Processing appointments with date <= {$today->toDateString()}");

        $this->info('=== Appointment Status Update Started ===');
        $this->info("Run at: {$now->toDateTimeString()}");

        $appointments = Appointment::where('status', 'confirmed')
            ->whereDate('date', '<=', $today)
            ->get();

        if ($appointments->isEmpty()) {
            $this->warn('No confirmed appointments found for today or earlier.');
            $logger->info('No appointments to process.');

            $this->info('=== Finished: Nothing to do ===');

            return;
        }

        $count = $appointments->count();
        $appointmentIds = $appointments->pluck('id')->toArray();

        Appointment::whereIn('id', $appointmentIds)
            ->update([
                'status' => 'completed',
                'token' => null,
            ]);

        $this->info("Successfully updated {$count} appointment(s) to 'completed' and cleared tokens.");

        $this->table(
            ['ID', 'Customer', 'Date', 'Time', 'Booking Code'],
            $appointments->map(fn ($a) => [
                $a->id,
                $a->customer_name,
                $a->date->format('M j, Y'),
                "{$a->start_time} - {$a->end_time}",
                $a->booking_code ?? 'N/A',
            ])
        );

        $logger->info("Updated {$count} appointments to completed and cleared tokens", [
            'appointment_ids' => $appointmentIds,
            'completed_at' => Carbon::now()->toDateTimeString(),
        ]);

        $logger->info('=== Appointment Status Update Finished ===');
        $this->info("=== Appointment Status Update Finished ({$count} updated) ===");
    }
}
