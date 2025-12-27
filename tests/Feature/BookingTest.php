<?php

use App\Livewire\Booking\Booking;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\BranchSchedule;
use App\Models\Service;
use Carbon\Carbon;
use Livewire\Livewire;

it('allows a guest to complete the full booking flow', function () {
    // === Setup Test Data ===
    Carbon::setTestNow('2025-12-25'); // Today is Dec 25, 2025

    $branch = Branch::factory()->create(['name' => 'Downtown Salon']);

    $service = Service::factory()->create([
        'name' => 'Haircut',
        'duration' => 60,
        'price' => 50.00,
    ]);

    // Schedule: Friday (Dec 26, 2025) from 09:00 to 17:00, 30-min intervals
    BranchSchedule::factory()->create([
        'branch_id' => $branch->id,
        'day_of_week' => 5, // Friday
        'start_time' => '09:00',
        'end_time' => '17:00',
        'slot_interval_minutes' => 30,
        'is_available' => true,
    ]);

    // === Test the Livewire Component ===
    Livewire::test(Booking::class)
        // Initial state
        ->assertSet('step', 1)
        ->assertSet('branchId', '')
        ->assertSet('serviceId', '')

        // Select branch and service
        ->set('branchId', (string) $branch->id)
        ->set('serviceId', (string) $service->id)
        ->call('nextStep')
        ->assertSet('step', 2)

        // Pick date
        ->set('date', '2025-12-26')

        // Slots load
        ->assertSet('availableSlots', fn ($slots) => count($slots) > 0)

        // Select time
        ->set('selectedTime', '10:00')
        ->call('nextStep')
        ->assertSet('step', 3)

        // Fill customer details
        ->set('customer_name', 'John Doe')
        ->set('customer_email', 'john@example.com')
        ->set('customer_phone', '+1234567890')
        ->set('notes', 'Please be gentle')

        // Submit booking
        ->call('book')
        ->assertHasNoErrors();

    // === Database Assertions ===
    $this->assertDatabaseHas('appointments', [
        'branch_id' => $branch->id,
        'service_id' => $service->id,
        'date' => '2025-12-26 00:00:00',
        'start_time' => '10:00',
        'end_time' => '11:00',
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'customer_phone' => '+1234567890',
        'notes' => 'Please be gentle',
        'status' => 'confirmed',
    ]);

    // === Booking Code Specific Assertions ===
    $appointment = Appointment::latest('id')->first();

    // 1. booking_code exists and is not null
    expect($appointment->booking_code)->not->toBeNull();

    // 2. Is valid hex color format: # followed by 6 hex digits
    expect($appointment->booking_code)
        ->toMatch('/^#[0-9A-F]{6}$/i')
        ->toStartWith('#')
        ->toHaveLength(7);

    // 3. Is unique (no duplicates)
    $this->assertDatabaseCount('appointments', 1); // Only one appointment
    $this->assertDatabaseMissing('appointments', [
        'booking_code' => $appointment->booking_code,
        'id' => '!=', $appointment->id,
    ]);

    // 4. Token also exists
    expect($appointment->token)->toBeString()->not->toBeEmpty();

    // === Form Reset After Booking ===
    Livewire::test(Booking::class)
        ->assertSet('step', 1)
        ->assertSet('branchId', '')
        ->assertSet('serviceId', '')
        ->assertSet('date', '')
        ->assertSet('selectedTime', null)
        ->assertSet('customer_name', '')
        ->assertSet('customer_email', '')
        ->assertSet('customer_phone', '')
        ->assertSet('notes', '');
});

it('prevents booking on a date with no available slots', function () {
    $branch = Branch::factory()->create();
    $service = Service::factory()->create(['duration' => 60]);

    // No schedule → no slots
    Livewire::test(Booking::class)
        ->set('branchId', (string) $branch->id)
        ->set('serviceId', (string) $service->id)
        ->call('nextStep')
        ->set('date', '2025-12-26')
        ->assertSet('availableSlots', [])
        ->assertSee('No available slots on this date');
});

it('blocks same-day booking (if today is cutoff)', function () {
    Carbon::setTestNow('2025-12-25 19:00:00'); // After 6 PM

    $branch = Branch::factory()->create();
    $service = Service::factory()->create();

    Livewire::test(Booking::class)
        ->set('branchId', (string) $branch->id)
        ->set('serviceId', (string) $service->id)
        ->call('nextStep')
        ->set('date', '2025-12-25') // Today
        ->assertSet('availableSlots', []); // Should be blocked
});
