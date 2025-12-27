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
        <x-mary-table :headers="$headers" :rows="$users" expandable striped wire:model.live="expanded" show-empty-text
            empty-text="Nothing Here!">

            <!-- Custom cell rendering -->
            @scope('cell.name', $user)
                {{ $user->name }}
            @endscope

            @scope('cell.designation', $user)
                {{ $user->designation }}
            @endscope

            @scope('actions', $user)
                <div class="flex flex-col sm:flex-row gap-2 items-center justify-center">
                    <x-mary-button class="btn-xs btn-warning dark:btn-soft w-full sm:w-auto"
                        wire:click="loadUser({{ $user->id }})">
                        {{ __('Edit') }}
                    </x-mary-button>
                    <x-mary-button class="btn-xs btn-error dark:btn-soft w-full sm:w-auto"
                        wire:click="confirmDelete({{ $user->id }})">
                        {{ __('Delete') }}
                    </x-mary-button>
                </div>
            @endscope

            @scope('expansion', $user)
                <div class="bg-base-200 dark:bg-zinc-700 rounded-lg overflow-hidden">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">

                        <!-- Left Column -->
                        <div class="space-y-3">
                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Name</span>
                                <p class="mt-1 text-sm font-medium">{{ $user->name ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 ">Email</span>
                                <p class="mt-1 text-sm font-medium">{{ $user->email ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 ">Designation</span>
                                <p class="mt-1 text-sm font-medium">{{ $user->designation ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Is admin</span>
                                <div class="mt-1">
                                    <div @class([
                                        'badge font-medium inline-flex items-center gap-1',
                                        'badge-warning text-warning-content' => $user->is_admin == 0,
                                        'badge-success text-success-content' => $user->is_admin == 1,
                                        'badge-ghost' => !in_array($user->is_admin, [1, 0]),
                                    ])>
                                        {{ $user->is_admin ? 'True' : 'False' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endscope

        </x-mary-table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $users->links() }}
        </div>

        @if ($users->isEmpty() && $searchInput)
            <div class="text-center text-gray-500 py-6">
                No user match your search "{{ $searchInput }}".
            </div>
        @endif

    </div>
</x-mary-card>
