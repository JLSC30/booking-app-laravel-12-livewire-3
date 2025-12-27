<?php

use App\Livewire\AppointmentController;
use App\Models\Appointment;
use App\Models\User;
use App\Policies\AppointmentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($this->admin);

    $this->appointment = Appointment::factory()->create([
        'status' => 'confirmed',
    ]);
});

/*
|--------------------------------------------------------------------------
| BASIC ACCESS
|--------------------------------------------------------------------------
*/

test('admin can view appointment list', function () {
    Livewire::test(AppointmentController::class)
        ->assertStatus(200);
});

test('non-admin cannot access appointment list', function () {
    $nonAdmin = User::factory()->create(['is_admin' => false]);
    $this->actingAs($nonAdmin);

    Livewire::test(AppointmentController::class)
        ->assertForbidden();
});

/*
|--------------------------------------------------------------------------
| STATUS UPDATES
|--------------------------------------------------------------------------
*/

test('it initializes currentStatus on render', function () {
    Livewire::test(AppointmentController::class)
        ->assertSet("currentStatus.{$this->appointment->id}", 'confirmed');
});

test('it can update appointment status to completed', function () {
    Livewire::test(AppointmentController::class)
        ->call('updateStatus', $this->appointment->id, 'completed')
        ->assertHasNoErrors();

    expect($this->appointment->refresh()->status)->toBe('completed');
});

test('it can update appointment status to cancelled', function () {
    Livewire::test(AppointmentController::class)
        ->call('updateStatus', $this->appointment->id, 'cancelled')
        ->assertHasNoErrors();

    expect($this->appointment->refresh()->status)->toBe('cancelled');
});

/*
|--------------------------------------------------------------------------
| FINAL STATE BEHAVIOR
|--------------------------------------------------------------------------
*/

test('it cannot change status after reaching a final state', function () {
    $component = Livewire::test(AppointmentController::class);

    $component->call('updateStatus', $this->appointment->id, 'confirmed')
        ->assertSet("currentStatus.{$this->appointment->id}", 'confirmed');

    $component->call('updateStatus', $this->appointment->id, 'completed')
        ->assertSet("currentStatus.{$this->appointment->id}", 'completed');

    // Attempt invalid transition
    $component->call('updateStatus', $this->appointment->id, 'cancelled');

    expect($this->appointment->refresh()->status)->toBe('completed');
});

/*
|--------------------------------------------------------------------------
| STATE TRACKING
|--------------------------------------------------------------------------
*/

test('it updates currentStatus property after status change', function () {
    Livewire::test(AppointmentController::class)
        ->call('updateStatus', $this->appointment->id, 'confirmed')
        ->assertSet("currentStatus.{$this->appointment->id}", 'confirmed');
});

/*
|--------------------------------------------------------------------------
| DATABASE INTEGRITY
|--------------------------------------------------------------------------
*/

test('it persists status change in database', function () {
    Livewire::test(AppointmentController::class)
        ->call('updateStatus', $this->appointment->id, 'confirmed');

    $this->assertDatabaseHas('appointments', [
        'id' => $this->appointment->id,
        'status' => 'confirmed',
    ]);
});

test('it only updates the status field', function () {
    $original = $this->appointment->only([
        'customer_name',
        'customer_email',
        'customer_phone',
        'booking_code',
        'date',
        'start_time',
        'end_time',
        'notes',
        'token',
    ]);

    Livewire::test(AppointmentController::class)
        ->call('updateStatus', $this->appointment->id, 'confirmed');

    $this->appointment->refresh();

    expect($this->appointment->status)->toBe('confirmed')
        ->and($this->appointment->only(array_keys($original)))
        ->toEqual($original);
});

/*
|--------------------------------------------------------------------------
| POLICY
|--------------------------------------------------------------------------
*/

test('policy allows admin to update and blocks non-admin', function () {
    $policy = new AppointmentPolicy;

    // Admin allowed
    expect($policy->update($this->admin, $this->appointment))->toBeTrue();

    // Non-admin blocked
    $nonAdmin = User::factory()->create(['is_admin' => false]);
    expect($policy->update($nonAdmin, $this->appointment))->toBeFalse();
});
