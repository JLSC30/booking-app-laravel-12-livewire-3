<?php

use App\Livewire\ForceChangePassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

// Import Laravel Pest helpers
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('redirects user with must_change_password=true to force change page', function () {
    $user = User::factory()->create([
        'must_change_password' => true,
    ]);

    actingAs($user)
        ->get('/dashboard')
        ->assertRedirect(route('password.force'));
});

it('allows user to change password successfully', function () {
    $user = User::factory()->create([
        'must_change_password' => true,
        'password' => Hash::make('OldPass123!'),
    ]);

    actingAs($user);

    Livewire::test(ForceChangePassword::class)
        ->set('password', 'NewPass123!')
        ->set('password_confirmation', 'NewPass123!')
        ->call('save')
        ->assertRedirect(route('dashboard'));

    expect($user->fresh()->must_change_password)->toBeFalse()
        ->and(Hash::check('NewPass123!', $user->fresh()->password))->toBeTrue();
});

it('prevents user from accessing force-password page after changing password', function () {
    $user = User::factory()->create([
        'must_change_password' => false,
    ]);

    actingAs($user)
        ->get(route('password.force'))
        ->assertRedirect(route('dashboard'));
});

it('prevents user from accessing other pages before changing password', function () {
    $user = User::factory()->create([
        'must_change_password' => true,
    ]);

    actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('password.force'));
});
