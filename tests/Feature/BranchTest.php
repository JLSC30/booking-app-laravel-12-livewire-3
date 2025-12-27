<?php

use App\Livewire\BranchController;
use App\Models\Branch;
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

it('allows admin to viewAny branches', function () {
    $policy = new \App\Policies\BranchPolicy;

    expect($policy->viewAny($this->admin))->toBeTrue();
    expect($policy->viewAny($this->regularUser))->toBeFalse();
});

it('allows admin to create branches', function () {
    $policy = new \App\Policies\BranchPolicy;

    expect($policy->create($this->admin))->toBeTrue();
    expect($policy->create($this->regularUser))->toBeFalse();
});

it('allows admin to update any branch', function () {
    $branch = Branch::factory()->create();
    $policy = new \App\Policies\BranchPolicy;

    expect($policy->update($this->admin, $branch))->toBeTrue();
    expect($policy->update($this->regularUser, $branch))->toBeFalse();
});

it('allows admin to delete any branch', function () {
    $branch = Branch::factory()->create();
    $policy = new \App\Policies\BranchPolicy;

    expect($policy->delete($this->admin, $branch))->toBeTrue();
    expect($policy->delete($this->regularUser, $branch))->toBeFalse();
});

// ========================
// CRUD TESTS (Livewire)
// ========================

it('can render the branch index page with branches', function () {
    actingAs($this->admin);

    Branch::factory()->count(3)->create();

    Livewire::test(BranchController::class)
        ->assertSee(Branch::inRandomOrder()->first()->name); // Random to avoid flakiness
});

it('can create a new branch', function () {
    actingAs($this->admin);

    Livewire::test(BranchController::class)
        ->set('name', 'Main Branch')
        ->set('address', '123 Downtown')
        ->set('is_active', true)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('branches', [
        'name' => 'Main Branch',
        'address' => '123 Downtown',
        'is_active' => true,
    ]);
});

it('can update an existing branch', function () {
    actingAs($this->admin);

    $branch = Branch::factory()->create([
        'name' => 'Old Name',
        'address' => 'Old Address',
        'is_active' => false,
    ]);

    Livewire::test(BranchController::class)
        ->call('loadBranch', $branch)
        ->assertSet('name', 'Old Name')
        ->assertSet('address', 'Old Address')
        ->assertSet('is_active', false)
        ->assertSet('branchId', $branch->id)
        ->set('name', 'New Name')
        ->set('address', 'New Address')
        ->set('is_active', true)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('branches', [
        'id' => $branch->id,
        'name' => 'New Name',
        'address' => 'New Address',
        'is_active' => true,
    ]);
});

it('resets form after save', function () {
    actingAs($this->admin);

    $branch = Branch::factory()->create();

    Livewire::test(BranchController::class)
        ->call('loadBranch', $branch)
        ->set('name', 'Updated Name')
        ->call('save')
        ->assertSet('branchId', null)
        ->assertSet('name', '')
        ->assertSet('address', '')
        ->assertSet('is_active', 1);
});

it('can delete a branch via modal', function () {
    actingAs($this->admin);

    $branch = Branch::factory()->create(['name' => 'To Be Deleted']);

    Livewire::test(BranchController::class)
        ->call('confirmDelete', $branch->id)
        ->assertSet('showDeleteModal', true)
        ->assertSet('deleteBranchId', $branch->id)
        ->assertSet('name', 'To Be Deleted')
        ->call('delete')
        ->assertSet('showDeleteModal', false)
        ->assertSet('deleteBranchId', null)
        ->assertSet('name', '');

    $this->assertDatabaseMissing('branches', ['id' => $branch->id]);
});

it('prevents unauthorized user from accessing branch page', function () {
    actingAs($this->regularUser);

    Livewire::test(BranchController::class)
        ->assertStatus(403);
});

it('prevents unauthorized user from creating branch', function () {
    actingAs($this->regularUser);

    Livewire::test(BranchController::class)
        ->assertStatus(403);
});

it('prevents unauthorized user from updating branch', function () {
    actingAs($this->regularUser);

    Branch::factory()->create();

    Livewire::test(BranchController::class)
        ->assertStatus(403);
});

it('prevents unauthorized user from deleting branch', function () {
    actingAs($this->regularUser);

    Branch::factory()->create();

    Livewire::test(BranchController::class)
        ->assertStatus(403);
});
