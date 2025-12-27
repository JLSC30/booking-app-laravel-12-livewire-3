<?php

use App\Livewire\AppointmentController;
use App\Livewire\Booking\Booking;
use App\Livewire\Booking\Cancel;
use App\Livewire\BranchController;
use App\Livewire\BranchScheduleController;
use App\Livewire\ExportController;
use App\Livewire\ForceChangePassword;
use App\Livewire\ServiceController;
use App\Livewire\UserController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    Route::get('/branches', BranchController::class)->name('branches');
    Route::get('/branches/schedules', BranchScheduleController::class)->name('branch-schedules');
    Route::get('/services', ServiceController::class)->name('services');
    Route::get('/users', UserController::class)->name('users');
    Route::get('/exports', ExportController::class)->name('exports');
    Route::get('/appointments', AppointmentController::class)->name('appointments');

    Route::get('/force-password-change', ForceChangePassword::class)
        ->name('password.force');
});

Route::get('/booking', Booking::class)->name('book');
Route::get('/booking/cancel/{token}', Cancel::class)
    ->name('booking.cancel')
    ->middleware('signed');
