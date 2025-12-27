<?php

use App\Models\User;
use Laravel\Fortify\Features;

test('login screen can be rendered', function () {
    $response = $this->get(route('login'));

    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $this->startSession(); // bootstraps the session for csrf_token()

    $user = User::factory()->withoutTwoFactor()->create([
        'password' => bcrypt('password'),
    ]);

    $response = $this->post(route('login.store'), [
        '_token' => session()->token(), // use session()->token(), not csrf_token()
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

test('users can not authenticate with invalid password', function () {
    $this->startSession();

    $user = User::factory()->create([
        'password' => bcrypt('password'),
    ]);

    $response = $this->post(route('login.store'), [
        '_token' => session()->token(),
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertSessionHasErrorsIn('login'); // Fortify uses "login" error bag
    $this->assertGuest();
});

test('users with two factor enabled are redirected to two factor challenge', function () {
    if (! Features::canManageTwoFactorAuthentication()) {
        $this->markTestSkipped('Two-factor authentication is not enabled.');
    }

    $this->startSession();

    $user = User::factory()->create([
        'password' => bcrypt('password'),
        'two_factor_secret' => encrypt('secret'),
        'two_factor_recovery_codes' => json_encode(['code1', 'code2']),
    ]);

    $response = $this->post(route('login.store'), [
        '_token' => session()->token(),
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertRedirect(route('two-factor.login'));
    $this->assertGuest();
});

test('users can logout', function () {
    $this->startSession();
    $user = User::factory()->create();
    $response = $this->actingAs($user)->post(route('logout'), ['_token' => session()->token()]);
    $response->assertRedirect(route('home'));
    $this->assertGuest();
});
