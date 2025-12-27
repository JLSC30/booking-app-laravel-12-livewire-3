<div class="p-6 space-y-6">
    <!-- FORM -->
    @include('livewire.branch.branch-form')

    <!-- DELETE MODAL -->
    <x-mary-modal wire:model="showDeleteModal" title="Delete Branch">
        <p>Are you sure you want to delete <strong>{{ $name }}</strong>?</p>

        <x-slot:actions>
            <x-mary-button class="btn-ghost" @click="$wire.showDeleteModal = false">
                {{ __('Cancel') }}
            </x-mary-button>
            <x-mary-button class="btn-error" wire:click="delete">
                {{ __('Delete') }}
            </x-mary-button>
        </x-slot:actions>
    </x-mary-modal>


    <!-- TABLE -->
    @include('livewire.branch.branch-table')
</div>
