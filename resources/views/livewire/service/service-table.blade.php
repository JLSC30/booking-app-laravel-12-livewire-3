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
        <x-mary-table :headers="$headers" :rows="$services" expandable striped wire:model.live="expanded" show-empty-text
            empty-text="Nothing Here!">

            <!-- Custom cell rendering -->
            @scope('cell.name', $service)
                {{ $service->name }}
            @endscope

            @scope('cell.price', $service)
                {{ config('app.currency') }} {{ number_format($service->price, 2) }}
            @endscope

            @scope('actions', $service)
                <div class="flex flex-col sm:flex-row gap-2 items-center justify-center">
                    <x-mary-button class="btn-xs btn-warning dark:btn-soft w-full sm:w-auto"
                        wire:click="loadService({{ $service->id }})">
                        {{ __('Edit') }}
                    </x-mary-button>
                    <x-mary-button class="btn-xs btn-error dark:btn-soft w-full sm:w-auto"
                        wire:click="confirmDelete({{ $service->id }})">
                        {{ __('Delete') }}
                    </x-mary-button>
                </div>
            @endscope

            @scope('expansion', $service)
                <div class="bg-base-200 dark:bg-zinc-700 rounded-lg overflow-hidden">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">

                        <!-- Left Column -->
                        <div class="space-y-3">
                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Service Name</span>
                                <p class="mt-1 text-sm font-medium">{{ $service->name ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 ">Description</span>
                                <p class="mt-1 text-sm font-medium">{{ $service->description ?? 'N/A' }}</p>

                            </div>

                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Price</span>

                                <p class="mt-1 text-sm font-medium">{{ config('app.currency') }}
                                    {{ number_format($service->price, 2) }}</p>
                            </div>

                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Duration</span>

                                <p class="mt-1 text-sm font-medium">
                                    {{ $service->duration >= 60 ? floor($service->duration / 60) . ' h ' . ($service->duration % 60 ? $service->duration % 60 . ' min' : '') : $service->duration . ' min' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endscope

        </x-mary-table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $services->links() }}
        </div>

        @if ($services->isEmpty() && $searchInput)
            <div class="text-center text-gray-500 py-6">
                No service match your search "{{ $searchInput }}".
            </div>
        @endif

    </div>
</x-mary-card>
