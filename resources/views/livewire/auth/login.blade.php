<x-layouts.auth>
    <x-mary-card class="bg-base-200 dark:bg-zinc-800" shadow>
        <div class="flex flex-col gap-6">
            <x-auth-header :title="__('Log in to your account')" :description="__('Enter your email and password below to log in')" />

            <!-- Session Status -->
            <x-auth-session-status class="text-center" :status="session('status')" />

            <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
                @csrf

                <!-- Email Address -->
                <x-mary-input name="email" wire:model="email" :label="__('Email address')" type="email" autofocus
                    placeholder="email@example.com" class="dark:bg-zinc-800" />

                <!-- Password -->
                <div class="relative">
                    <x-mary-password name="password" wire:model="password" :label="__('Password')" :placeholder="__('Password')" right
                        class="dark:bg-zinc-800" />

                    @if (Route::has('password.request'))
                        <flux:link class="absolute mt-2 top-0 text-xs end-0 text-secondary"
                            :href="route('password.request')" wire:navigate>
                            {{ __('Forgot your password?') }}
                        </flux:link>
                    @endif
                </div>

                <!-- Remember Me -->
                {{-- <flux:checkbox name="remember" class="text-secondary" :label="__('Remember me')" :checked="old('remember')" /> --}}
                <x-mary-checkbox label="{{ __('Remember me') }}" wire:model="remember"
                    class="checkbox-sm checkbox-secondary " />


                <div class="flex items-center justify-end">
                    <x-mary-button type="submit" class="w-full btn-neutral dark:btn-secondary"
                        data-test="login-button">
                        {{ __('Log in') }}
                    </x-mary-button>
                </div>
            </form>

            @if (Route::has('register'))
                <div class="space-x-1 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                    <span>{{ __('Don\'t have an account?') }}</span>
                    <flux:link :href="route('register')" class="text-secondary" wire:navigate>{{ __('Sign up') }}
                    </flux:link>
                </div>
            @endif
        </div>
    </x-mary-card>
</x-layouts.auth>
