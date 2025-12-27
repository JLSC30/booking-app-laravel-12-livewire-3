<x-layouts.auth>
    <x-mary-card class="bg-base-200 dark:bg-zinc-800" shadow>
        <div class="flex flex-col gap-6">
            <x-auth-header :title="__('Reset password')" :description="__('Please enter your new password below')" />

            <!-- Session Status -->
            <x-auth-session-status class="text-center" :status="session('status')" />

            <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-6">
                @csrf
                <!-- Token -->
                <input type="hidden" name="token" value="{{ request()->route('token') }}">

                <!-- Email Address -->
                <x-mary-input name="email" wire:model="email" :label="__('Email address')" :value="old('email')" type="email"
                    placeholder="email@example.com" class="dark:bg-zinc-800" />

                <!-- Password -->
                <x-mary-password name="password" wire:model="password" :label="__('Password')" :placeholder="__('Password')"
                    class="dark:bg-zinc-800" right />
                <!-- Confirm Password -->
                <x-mary-password name="password_confirmation" wire:model="password_confirmation" :label="__('Confirm password')"
                    :placeholder="__('Confirm password')" right />

                <div class="flex items-center justify-end">
                    <x-mary-button type="submit" class="w-full btn-neutral dark:btn-secondary"
                        data-test="reset-password-button">
                        {{ __('Reset password') }}
                    </x-mary-button>
                </div>
            </form>
        </div>
    </x-mary-card>
</x-layouts.auth>
