<x-layouts.auth>
    <x-mary-card class="bg-base-200 dark:bg-zinc-800" shadow>
        <div class="flex flex-col gap-6">
            <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

            <!-- Session Status -->
            <x-auth-session-status class="text-center" :status="session('status')" />

            <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
                @csrf
                <!-- Name -->
                <x-mary-input name="name" wire:model="name" :label="__('Name')" :value="old('name')" type="text" autofocus
                    :placeholder="__('Full name')" class="dark:bg-zinc-800" />

                <!-- Email Address -->
                <x-mary-input name="email" wire:model="email" :label="__('Email address')" :value="old('email')" type="email"
                    placeholder="email@example.com" class="dark:bg-zinc-800" />
                <!-- Password -->
                <x-mary-password name="password" wire:model="password" :label="__('Password')" :placeholder="__('Password')" right
                    class="dark:bg-zinc-800" />

                <!-- Confirm Password -->
                <x-mary-password name="password_confirmation" wire:model="password_confirmation" :label="__('Confirm password')"
                    :placeholder="__('Confirm password')" right class="dark:bg-zinc-800" />
                <div class="flex items-center justify-end">
                    <x-mary-button type="submit" class="w-full btn-neutral dark:btn-secondary"
                        data-test="register-user-button">
                        {{ __('Create account') }}
                    </x-mary-button>
                </div>
            </form>

            <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
                <span>{{ __('Already have an account?') }}</span>
                <flux:link :href="route('login')" class="text-secondary" wire:navigate>{{ __('Log in') }}</flux:link>
            </div>
        </div>
    </x-mary-card>
</x-layouts.auth>
