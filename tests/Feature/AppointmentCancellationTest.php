<?php

use App\Livewire\Booking\Cancel;
use App\Mail\AppointmentConfirmation;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

beforeEach(function () {
    Carbon::setTestNow('2025-12-27 10:00:00'); // Today = Dec 27, 2025
});

it('shows confirmation page for valid signed link with future appointment', function () {
    $appointment = createConfirmedAppointment('2025-12-28'); // Tomorrow

    // Mock hasValidSignature to return true for valid cases
    Request::macro('hasValidSignature', fn () => true);

    Livewire::test(Cancel::class, ['token' => $appointment->token])
        ->assertSee('Cancel Your Appointment?')
        ->assertSee($appointment->date->format('F j, Y'))
        ->assertSee('Yes, Cancel Appointment')
        ->assertSee('No, Keep Appointment');
});

it('rejects unsigned or invalid signature links', function () {
    $appointment = createConfirmedAppointment('2025-12-28');

    // Mock hasValidSignature to return false
    Request::macro('hasValidSignature', fn () => false);

    Livewire::test(Cancel::class, ['token' => $appointment->token])
        ->assertSee('This cancellation link is invalid or has expired.');
});

it('blocks already cancelled appointments', function () {
    $appointment = createConfirmedAppointment('2025-12-28');
    $appointment->update(['status' => 'cancelled']);

    Request::macro('hasValidSignature', fn () => true);

    Livewire::test(Cancel::class, ['token' => $appointment->token])
        ->assertSee('This appointment has already been cancelled.');
});

it('blocks past appointments', function () {
    $appointment = createConfirmedAppointment('2025-12-26'); // Yesterday

    Request::macro('hasValidSignature', fn () => true);

    Livewire::test(Cancel::class, ['token' => $appointment->token])
        ->assertSee('Sorry, past appointments cannot be cancelled.');
});

it('successfully cancels appointment when user confirms', function () {
    $appointment = createConfirmedAppointment('2025-12-28');

    Request::macro('hasValidSignature', fn () => true);

    Livewire::test(Cancel::class, ['token' => $appointment->token])
        ->assertSee('Yes, Cancel Appointment')
        ->call('confirmCancellation')
        ->assertSee('Your appointment has been successfully cancelled.')
        ->assertSee('Appointment Cancelled');

    $appointment->refresh();
    expect($appointment->status)->toBe('cancelled')
        ->and($appointment->token)->toBeNull();
});

it('does not generate cancellation link in email for today\'s appointments', function () {
    Mail::fake();

    $appointment = createConfirmedAppointment('2025-12-27'); // Today

    Mail::to('customer@example.com')->queue(new AppointmentConfirmation($appointment));

    Mail::assertQueued(AppointmentConfirmation::class, function ($mail) use ($appointment) {
        return $mail->cancelUrl === null &&
               $mail->canCancelUntil === null &&
               $mail->appointment->is($appointment);
    });
});

it('generates cancellation link in email for future appointments', function () {
    Mail::fake();

    $appointment = createConfirmedAppointment('2025-12-28'); // Tomorrow

    Mail::to('customer@example.com')->queue(new AppointmentConfirmation($appointment));

    Mail::assertQueued(AppointmentConfirmation::class, function ($mail) use ($appointment) {
        return $mail->cancelUrl !== null &&
               str_contains($mail->cancelUrl, $appointment->token) &&
               $mail->canCancelUntil === 'December 27, 2025';
    });
});

function createConfirmedAppointment(string $date): Appointment
{
    $branch = Branch::factory()->create(['name' => 'Main Branch']);
    $service = Service::factory()->create(['name' => 'Haircut']);

    return Appointment::factory()->create([
        'branch_id' => $branch->id,
        'service_id' => $service->id,
        'date' => $date,
        'start_time' => '09:00',
        'end_time' => '10:00',
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'status' => 'confirmed',
        'token' => \Str::random(64),
        'booking_code' => '#ABC123',
    ]);
}