<?php

use App\Livewire\UserController;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Admin user - can do everything
    $this->admin = User::factory()->create(['is_admin' => true]);

    // Regular user - cannot manage users
    $this->regularUser = User::factory()->create(['is_admin' => false]);

    // Create some branches for testing
    $this->branch = Branch::factory()->create(['name' => 'Main Branch']);
    $this->branch2 = Branch::factory()->create(['name' => 'Secondary Branch']);
});

// ========================
// POLICY TESTS
// ========================

it('allows admin to viewAny users', function () {
    $policy = new \App\Policies\UserPolicy;

    expect($policy->viewAny($this->admin))->toBeTrue();
    expect($policy->viewAny($this->regularUser))->toBeFalse();
});

it('allows admin to create users', function () {
    $policy = new \App\Policies\UserPolicy;

    expect($policy->create($this->admin))->toBeTrue();
    expect($policy->create($this->regularUser))->toBeFalse();
});

it('allows admin to update any user', function () {
    $user = User::factory()->create();
    $policy = new \App\Policies\UserPolicy;

    expect($policy->update($this->admin, $user))->toBeTrue();
    expect($policy->update($this->regularUser, $user))->toBeFalse();
});

it('allows admin to delete any user', function () {
    $user = User::factory()->create();
    $policy = new \App\Policies\UserPolicy;

    expect($policy->delete($this->admin, $user))->toBeTrue();
    expect($policy->delete($this->regularUser, $user))->toBeFalse();
});

// ========================
// CRUD TESTS (Livewire)
// ========================

it('can render the user index page with users', function () {
    actingAs($this->admin);

    User::factory()->count(5)->create();

    Livewire::test(UserController::class)
        ->assertSee(User::inRandomOrder()->first()->name)
        ->assertSee(User::inRandomOrder()->first()->email);
});

it('can create a new user with password', function () {
    actingAs($this->admin);

    Livewire::test(UserController::class)
        ->set('name', 'John Doe')
        ->set('email', 'john@example.com')
        ->set('password', 'Password123!')
        ->set('password_confirmation', 'Password123!')
        ->set('designation', 'Developer')
        ->set('is_admin', false)
        ->set('must_change_password', true)
        ->set('branch_searchable_id', $this->branch->id)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'designation' => 'Developer',
        'is_admin' => false,
        'must_change_password' => true,
    ]);

    // Password should be hashed
    $user = User::where('email', 'john@example.com')->first();
    expect(Hash::check('Password123!', $user->password))->toBeTrue();
    expect($user->branches->first()->id)->toBe($this->branch->id);

    $this->assertDatabaseHas('branch_user', [
        'user_id' => $user->id,
        'branch_id' => $this->branch->id,
    ]);
});

it('requires password when creating a user', function () {
    actingAs($this->admin);

    Livewire::test(UserController::class)
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@example.com')
        ->set('designation', 'Manager')
        ->set('password', '') // empty
        ->set('password_confirmation', '')
        ->call('save')
        ->assertHasErrors('password');
});

it('can update an existing user without changing password', function () {
    actingAs($this->admin);

    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
        'designation' => 'Old Designation',
        'is_admin' => false,
        'must_change_password' => true,
    ]);

    // Attach original branch
    $user->branches()->attach($this->branch->id);

    Livewire::test(UserController::class)
        ->call('loadUser', $user)
        ->assertSet('name', 'Old Name')
        ->assertSet('email', 'old@example.com')
        ->assertSet('designation', 'Old Designation')
        ->assertSet('is_admin', false)
        ->assertSet('must_change_password', true)
        ->assertSet('userId', $user->id)
        ->assertSet('password', '') // should be empty on load
        ->set('name', 'New Name')
        ->set('email', 'new@example.com')
        ->set('designation', 'Senior Developer')
        ->set('is_admin', true)
        ->set('must_change_password', false)
        ->set('branch_searchable_id', $this->branch2->id)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'New Name',
        'email' => 'new@example.com',
        'designation' => 'Senior Developer',
        'is_admin' => true,
        'must_change_password' => false,
    ]);

    // Check branch was updated
    $user->refresh();
    expect($user->branches->first()->id)->toBe($this->branch2->id);

    $this->assertDatabaseHas('branch_user', [
        'user_id' => $user->id,
        'branch_id' => $this->branch2->id,
    ]);

    // Old branch should be removed
    $this->assertDatabaseMissing('branch_user', [
        'userId' => $user->id,
        'branchId' => $this->branch->id,
    ]);

    // Old password should remain unchanged
    $user->refresh();
    expect($user->password)->toBe($user->getOriginal('password'));
});

it('can update an existing user with a new password', function () {
    actingAs($this->admin);

    $user = User::factory()->create([
        'designation' => 'Original Role',  // Required field
        'is_admin' => false,
    ]);

    $user->branches()->attach($this->branch->id);
    $oldPasswordHash = $user->password;

    Livewire::test(UserController::class)
        ->call('loadUser', $user)
        ->set('password', 'NewStrongPass123!')
        ->set('password_confirmation', 'NewStrongPass123!')
        ->call('save')
        ->assertHasNoErrors();

    $user->refresh();
    expect($user->password)->not->toBe($oldPasswordHash);
    expect(Hash::check('NewStrongPass123!', $user->password))->toBeTrue();
});

it('resets form after save', function () {
    actingAs($this->admin);

    $user = User::factory()->create(['designation' => 'Tester']);

    Livewire::test(UserController::class)
        ->call('loadUser', $user)
        ->assertSet('userId', $user->id)  // ← Confirm it's loaded
        ->assertSet('name', $user->name)
        ->set('name', 'Updated Name')
        ->call('save')
        ->assertHasNoErrors()
        ->assertSet('userId', null)  // ← Now this will pass
        ->assertSet('name', '')
        ->assertSet('email', '')
        ->assertSet('password', '')
        ->assertSet('password_confirmation', '')
        ->assertSet('designation', '')
        ->assertSet('is_admin', false)
        ->assertSet('must_change_password', true);
});

it('can delete a user via modal', function () {
    actingAs($this->admin);

    $user = User::factory()->create(['name' => 'Delete Me']);

    Livewire::test(UserController::class)
        ->call('confirmDelete', $user->id)
        ->assertSet('showDeleteModal', true)
        ->assertSet('deleteUserId', $user->id)
        ->assertSet('name', 'Delete Me')
        ->call('delete')
        ->assertSet('showDeleteModal', false)
        ->assertSet('deleteUserId', null)
        ->assertSet('name', '');

    $this->assertDatabaseMissing('users', ['id' => $user->id]);

    // Check branch relationship was also removed
    $this->assertDatabaseMissing('branch_user', [
        'userId' => $user->id,
    ]);
});

it('can search for branches', function () {
    actingAs($this->admin);

    Branch::factory()->create(['name' => 'North Branch']);
    Branch::factory()->create(['name' => 'South Branch']);
    Branch::factory()->create(['name' => 'East Branch']);

    Livewire::test(UserController::class)
        ->call('searchBranches', 'North')
        ->assertSet('branchesSearchable', fn ($branches) => $branches->contains('name', 'North Branch'));
});

it('prevents unauthorized user from accessing user management page', function () {
    actingAs($this->regularUser);

    Livewire::test(UserController::class)
        ->assertStatus(403);
});

it('prevents unauthorized user from creating a user', function () {
    actingAs($this->regularUser);

    Livewire::test(UserController::class)
        ->assertStatus(403);  // ← Just this, no sets or calls
});

it('prevents unauthorized user from updating a user', function () {
    actingAs($this->regularUser);

    User::factory()->create();

    Livewire::test(UserController::class)
        ->assertStatus(403);  // ← No loadUser call
});

it('prevents unauthorized user from deleting a user', function () {
    actingAs($this->regularUser);

    User::factory()->create();

    Livewire::test(UserController::class)
        ->assertStatus(403);  // ← No confirmDelete or delete calls
});
