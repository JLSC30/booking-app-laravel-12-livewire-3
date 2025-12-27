<!-- CREATE / UPDATE FORM -->
<x-mary-card class="bg-base-200 dark:bg-zinc-800" shadow>
    <h2 class="text-xl font-semibold mb-4 text-neutral/80 dark:text-gray-100">{{ $userId ? 'Edit User' : 'Create User' }}
    </h2>

    <div class="space-y-3">
        <!-- User Name -->
        <x-mary-input label="{{ __('Name') }}" wire:model.defer="name" clearable class="dark:bg-zinc-800" />

        <!-- Email -->
        <x-mary-input label="{{ __('Email') }}" wire:model.defer="email" clearable class="dark:bg-zinc-800" />

        <!-- Password -->
        <x-mary-password label="{{ __('Password') }}" type="password" wire:model.defer="password" right
            class="dark:bg-zinc-800" />
        <x-mary-password label="{{ __('Confirm Password') }}" type="password" wire:model.defer="password_confirmation"
            right class="dark:bg-zinc-800" />

        <!-- Designation -->
        <x-mary-input label="{{ __('Designation') }}" wire:model.defer="designation" clearable
            class="dark:bg-zinc-800" />

        <x-mary-choices label="Branch" wire:model="branch_searchable_id" :options="$branchesSearchable" placeholder="Search ..."
            hint="Start typing the branch name" icon="o-magnifying-glass" debounce="500ms" single searchable
            wire:search='searchBranches' option-value='id' option-label="name" class="dark:bg-zinc-800" />

        <!-- Is Admin -->
        <x-mary-toggle label="{{ __('Is Admin') }}" wire:model.defer="is_admin" class="toggle-secondary" />

        <!-- Must Change Password -->
        <x-mary-toggle label="{{ __('Require password change on next login') }}" wire:model.defer="must_change_password"
            class="toggle-secondary " />


        <!-- Buttons -->
        <div class="flex gap-2 justify-end">
            @if ($userId)
                <x-mary-button class="btn-ghost" wire:click="resetForm">
                    {{ __('Cancel') }}
                </x-mary-button>
            @endif
            <x-mary-button class="btn-neutral dark:btn-secondary!" wire:click="save">
                {{ $userId ? __('Update') : __('Create') }}
            </x-mary-button>
        </div>
    </div>
</x-mary-card>
