<x-mary-card class="bg-base-200 dark:bg-zinc-800" shadow>

    <!-- Loading skeleton -->
    <div wire:loading>
        @for ($i = 0; $i < 5; $i++)
            <div class="skeleton h-12 w-full rounded mb-2"></div>
        @endfor
    </div>

    <div wire:loading.remove class="space-y-4">

        <!-- Search input -->
        <div class="flex justify-center md:justify-end">
            <x-mary-input class="w-full md:w-64 dark:bg-zinc-800" placeholder="Search by name..."
                wire:model.live.debounce.500ms="searchInput" clearable />
        </div>

        <!-- MaryUI Table with expandable rows -->
        <x-mary-table :headers="$headers" :rows="$branches" expandable striped wire:model.live="expanded" show-empty-text
            empty-text="Nothing Here!">

            <!-- Custom cell rendering -->
            @scope('cell.name', $branch)
                {{ $branch->name }}
            @endscope

            @scope('actions', $branch)
                <div class="flex flex-col sm:flex-row gap-2 items-center justify-center">
                    <x-mary-button class="btn-xs btn-warning dark:btn-soft w-full sm:w-auto"
                        wire:click="loadBranch({{ $branch->id }})">
                        {{ __('Edit') }}
                    </x-mary-button>
                    <x-mary-button class="btn-xs btn-error dark:btn-soft w-full sm:w-auto"
                        wire:click="confirmDelete({{ $branch->id }})">
                        {{ __('Delete') }}
                    </x-mary-button>
                </div>
            @endscope

            @scope('expansion', $branch)
                <div class="bg-base-200 dark:bg-zinc-700 rounded-lg overflow-hidden">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">

                        <!-- Left Column -->
                        <div class="space-y-3">
                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Branch Name</span>
                                <p class="mt-1 text-sm font-medium">{{ $branch->name ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Is active</span>
                                <div class="mt-1">
                                    <div @class([
                                        'badge font-medium inline-flex items-center gap-1',
                                        'badge-warning text-warning-content' => $branch->is_active == 0,
                                        'badge-success text-success-content' => $branch->is_active == 1,
                                        'badge-ghost' => !in_array($branch->is_active, [1, 0]),
                                    ])>
                                        {{ $branch->is_active ? 'True' : 'False' }}
                                    </div>
                                </div>
                            </div>

                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Branch Address</span>
                                <p class="mt-1 text-sm font-medium">{{ $branch->address ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endscope

        </x-mary-table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $branches->links() }}
        </div>

        @if ($branches->isEmpty() && $searchInput)
            <div class="text-center text-gray-500 py-6">
                No branches match your search "{{ $searchInput }}".
            </div>
        @endif

    </div>
</x-mary-card>
