<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Volt\Component;

new class extends Component {
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => ['required', 'string', 'current_password'],
                'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        $this->dispatch('password-updated');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Update password')" :subheading="__('Ensure your account is using a long, random password to stay secure')">
        <form method="POST" wire:submit="updatePassword" class="mt-6 space-y-6">
            <x-mary-input type="password" :label="__('Current password')" wire:model="current_password" required
                class="dark:bg-zinc-800" />
            <x-mary-input type="password" :label="__('New password')" wire:model="password" required class="dark:bg-zinc-800" />
            <x-mary-input type="password" :label="__('Confirm Password')" wire:model="password_confirmation" required
                class="dark:bg-zinc-800" />

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <x-mary-button type="submit" class="w-full btn-secondary" data-test="update-password-button">
                        {{ __('Save') }}
                    </x-mary-button>
                </div>

                <x-action-message class="me-3" on="password-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>
    </x-settings.layout>
</section>
