<x-layouts.auth>
    <x-mary-card class="bg-base-200 dark:bg-zinc-800" shadow>
        <div class="flex flex-col gap-6">
            <x-auth-header :title="__('Forgot password')" :description="__('Enter your email to receive a password reset link')" />

            <!-- Session Status -->
            <x-auth-session-status class="text-center" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
                @csrf

                <!-- Email Address -->
                <x-mary-input name="email" wire:model="email" :label="__('Email address')" type="email" autofocus
                    placeholder="email@example.com" class="dark:bg-zinc-800" />

                <x-mary-button type="submit" class="w-full btn-neutral dark:btn-secondary"
                    data-test="email-password-reset-link-button">
                    {{ __('Email password reset link') }}
                </x-mary-button>
            </form>

            <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-400">
                <span>{{ __('Or, return to') }}</span>
                <flux:link :href="route('login')" class="text-secondary" wire:navigate>{{ __('Log in') }}</flux:link>
            </div>
        </div>
    </x-mary-card>
</x-layouts.auth>
