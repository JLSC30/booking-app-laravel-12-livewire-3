<div class="min-h-screen flex items-center justify-center">
    <x-mary-card class="bg-base-200 dark:bg-zinc-800 w-full max-w-md" shadow>
        <h2 class="text-lg font-semibold mb-4 text-center">
            Change Your Password
        </h2>
        <div class="space-y-3">
            <x-mary-password label="{{ __('Password') }}" type="password" wire:model.defer="password" right
                class="dark:bg-zinc-800" />
            <x-mary-password label="{{ __('Confirm Password') }}" type="password" wire:model.defer="password_confirmation"
                right class="dark:bg-zinc-800" />
            <div class="mt-6">
                <x-mary-button class="btn-neutral dark:!btn-secondary w-full" wire:click="save">
                    {{ __('Change Password') }}
                </x-mary-button>
            </div>
        </div>
    </x-mary-card>
</div>
