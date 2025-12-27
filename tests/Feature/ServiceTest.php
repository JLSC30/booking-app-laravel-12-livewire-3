<?php

use App\Livewire\ServiceController;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

// Import Laravel Pest helpers
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Admin user - can do everything
    $this->admin = User::factory()->create(['is_admin' => true]);

    // Regular user - restricted
    $this->regularUser = User::factory()->create(['is_admin' => false]);
});

// ========================
// POLICY TESTS
// ========================

it('allows admin to viewAny services', function () {
    $policy = new \App\Policies\ServicePolicy;

    expect($policy->viewAny($this->admin))->toBeTrue();
    expect($policy->viewAny($this->regularUser))->toBeFalse();
});

it('allows admin to create services', function () {
    $policy = new \App\Policies\ServicePolicy;

    expect($policy->create($this->admin))->toBeTrue();
    expect($policy->create($this->regularUser))->toBeFalse();
});

it('allows admin to update any service', function () {
    $service = Service::factory()->create();
    $policy = new \App\Policies\ServicePolicy;

    expect($policy->update($this->admin, $service))->toBeTrue();
    expect($policy->update($this->regularUser, $service))->toBeFalse();
});

it('allows admin to delete any service', function () {
    $service = Service::factory()->create();
    $policy = new \App\Policies\ServicePolicy;

    expect($policy->delete($this->admin, $service))->toBeTrue();
    expect($policy->delete($this->regularUser, $service))->toBeFalse();
});

// ========================
// CRUD TESTS (Livewire)
// ========================

it('can render the service index page with services', function () {
    actingAs($this->admin);

    Service::factory()->count(3)->create();

    Livewire::test(ServiceController::class)
        ->assertSee(Service::inRandomOrder()->first()->name); // Random to avoid flakiness
});

it('can create a new service', function () {
    actingAs($this->admin);

    Livewire::test(ServiceController::class)
        ->set('name', 'Main Service')
        ->set('description', 'A main service')
        ->set('price', 100.00)
        ->set('duration', 60)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('services', [
        'name' => 'Main Service',
        'description' => 'A main service',
        'price' => 100.00,
        'duration' => 60,
    ]);
});

it('can update an existing service', function () {
    actingAs($this->admin);

    $service = Service::factory()->create([
        'name' => 'Old Name',
        'description' => 'Old Description',
        'price' => 50.00,
        'duration' => 30,
    ]);

    Livewire::test(ServiceController::class)
        ->call('loadService', $service)
        ->assertSet('name', 'Old Name')
        ->assertSet('description', 'Old Description')
        ->assertSet('price', 50.00)
        ->assertSet('duration', 30)
        ->set('name', 'New Name')
        ->set('description', 'New Description')
        ->set('price', 100.00)
        ->set('duration', 60)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('services', [
        'id' => $service->id,
        'name' => 'New Name',
        'description' => 'New Description',
        'price' => 100.00,
        'duration' => 60,
    ]);
});

it('resets form after save', function () {
    actingAs($this->admin);

    $service = Service::factory()->create();

    Livewire::test(ServiceController::class)
        ->call('loadService', $service)
        ->set('name', 'Updated Name')
        ->call('save')
        ->assertSet('serviceId', null)
        ->assertSet('name', '')
        ->assertSet('description', '')
        ->assertSet('price', 0)
        ->assertSet('duration', 0);
});

it('can delete a service via modal', function () {
    actingAs($this->admin);

    $service = Service::factory()->create(['name' => 'To Be Deleted']);

    Livewire::test(ServiceController::class)
        ->call('confirmDelete', $service->id)
        ->assertSet('showDeleteModal', true)
        ->assertSet('deleteServiceId', $service->id)
        ->assertSet('name', 'To Be Deleted')
        ->call('delete')
        ->assertSet('showDeleteModal', false)
        ->assertSet('deleteServiceId', null)
        ->assertSet('name', '');

    $this->assertDatabaseMissing('services', ['id' => $service->id]);
});

it('prevents unauthorized user from accessing service page', function () {
    actingAs($this->regularUser);

    Livewire::test(ServiceController::class)
        ->assertStatus(403);
});

it('prevents unauthorized user from creating service', function () {
    actingAs($this->regularUser);

    Livewire::test(ServiceController::class)
        ->assertStatus(403);
});

it('prevents unauthorized user from updating service', function () {
    actingAs($this->regularUser);

    Service::factory()->create();

    Livewire::test(ServiceController::class)
        ->assertStatus(403);
});

it('prevents unauthorized user from deleting service', function () {
    actingAs($this->regularUser);

    Service::factory()->create();

    Livewire::test(ServiceController::class)
        ->assertStatus(403);
});
