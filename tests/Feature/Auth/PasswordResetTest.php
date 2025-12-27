<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

test('reset password link screen can be rendered', function () {
    $response = $this->get(route('password.request'));
    $response->assertStatus(200);
});

test('reset password link can be requested', function () {
    $this->startSession();
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.request'), [
        '_token' => session()->token(),
        'email' => $user->email,
    ]);

    Notification::assertSentTo($user, ResetPassword::class);
});

test('reset password screen can be rendered', function () {
    $this->startSession();
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.request'), [
        '_token' => session()->token(),
        'email' => $user->email,
    ]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        // password.reset route requires both token and email
        $response = $this->get(route('password.reset', [
            'token' => $notification->token,
            'email' => $user->email,
        ]));

        $response->assertStatus(200);

        return true;
    });
});

test('password can be reset with valid token', function () {
    $this->startSession();
    Notification::fake();

    $user = User::factory()->create([
        'password' => bcrypt('old-password'),
    ]);

    $this->post(route('password.request'), [
        '_token' => session()->token(),
        'email' => $user->email,
    ]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->post(route('password.update'), [
            '_token' => session()->token(),
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        return true;
    });
});
