<!-- CREATE / UPDATE FORM -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/gh/robsontenorio/mary@0.44.2/libs/currency/currency.js">
</script>

<x-mary-card class="bg-base-200 dark:bg-zinc-800" shadow>
    <h2 class="text-xl font-semibold mb-4 text-neutral/80 dark:text-gray-100">
        {{ $serviceId ? 'Edit Service' : 'Create Service' }}</h2>

    <div class="space-y-3">
        <!-- Service Name -->
        <x-mary-input label="{{ __('Service Name') }}" wire:model.defer="name" class="dark:bg-zinc-800" clearable />

        <!-- Description -->
        <x-mary-textarea wire:model.defer="description" label="{{ __('Description') }}" class="dark:bg-zinc-800" />

        <!-- Price -->
        <x-mary-input label="{{ __('Price') }}" wire:model.defer="price" prefix="PHP" class="dark:bg-zinc-800" money
            clearable />

        <!-- Duration -->
        <x-mary-input label="{{ __('Duration (minutes)') }}" type="number" min="30" max="180" step="10"
            wire:model.defer="duration" clearable class="dark:bg-zinc-800" />

        <!-- Buttons -->
        <div class="flex gap-2 justify-end">
            @if ($serviceId)
                <x-mary-button class="btn-ghost" wire:click="resetForm">
                    {{ __('Cancel') }}
                </x-mary-button>
            @endif
            <x-mary-button class="btn-neutral dark:btn-secondary!" wire:click="save">
                {{ $serviceId ? __('Update') : __('Create') }}
            </x-mary-button>
        </div>
    </div>
</x-mary-card>
