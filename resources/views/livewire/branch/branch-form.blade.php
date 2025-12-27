<!-- CREATE / UPDATE FORM -->
<x-mary-card class="bg-base-200 dark:bg-zinc-800" shadow>
    <h2 class="text-xl font-semibold mb-4 text-neutral/80 dark:text-gray-100">
        {{ $branchId ? 'Edit Branch' : 'Create Branch' }}</h2>

    <div class="space-y-3">
        <!-- Branch Name -->
        <x-mary-input label="{{ __('Branch Name') }}" wire:model.defer="name" clearable class="dark:bg-zinc-800" />

        <!-- Address -->
        {{-- <x-textarea class="w-full  input-secondary" wire:model.defer="address" placeholder="Address" hint="Max 1000 chars" rows="5" /> --}}
        <x-mary-input label="{{ __('Branch Address') }}" wire:model.defer="address" clearable class="dark:bg-zinc-800" />
        <!-- Active toggle -->
        <x-mary-toggle label="{{ __('Active') }}" wire:model.defer="is_active" class="toggle-secondary" />

        <!-- Buttons -->
        <div class="flex gap-2 justify-end">
            @if ($branchId)
                <x-mary-button class="btn-ghost" wire:click="resetForm">
                    {{ __('Cancel') }}
                </x-mary-button>
            @endif
            <x-mary-button class="btn-neutral dark:btn-secondary!" wire:click="save">
                {{ $branchId ? __('Update') : __('Create') }}
            </x-mary-button>
        </div>
    </div>
</x-mary-card>
