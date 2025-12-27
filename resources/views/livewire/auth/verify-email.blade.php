<x-layouts.auth>
    <x-mary-card class="bg-base-200 dark:bg-zinc-800" shadow>
        <div class="mt-4 flex flex-col gap-6">
            <flux:text class="text-center">
                {{ __('Please verify your email address by clicking on the link we just emailed to you.') }}
            </flux:text>

            @if (session('status') == 'verification-link-sent')
                <flux:text class="text-center font-medium !dark:text-green-400 !text-green-600">
                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                </flux:text>
            @endif

            <div class="flex flex-col items-center justify-between space-y-3">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <x-mary-button type="submit" class="w-full btn-neutral dark:btn-secondary">
                        {{ __('Resend verification email') }}
                    </x-mary-button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-mary-button type="submit" class="text-sm cursor-pointer btn-ghost" data-test="logout-button">
                        {{ __('Log out') }}
                    </x-mary-button>

                </form>
            </div>
        </div>
    </x-mary-card>
</x-layouts.auth>
