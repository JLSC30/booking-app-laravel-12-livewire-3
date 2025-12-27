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
            <x-mary-input class="w-full md:w-64 dark:bg-zinc-800" placeholder="Search by name, code or email..."
                wire:model.live.debounce.500ms="searchInput" clearable />
        </div>

        <!-- MaryUI Table with expandable rows -->
        <x-mary-table :headers="$headers" :rows="$appointments" expandable striped wire:model.live="expanded" show-empty-text
            empty-text="Nothing Here!">

            <!-- Custom cell rendering -->
            @scope('cell.booking_code', $appointment)
                <span class="px-2 py-1 rounded-lg font-bold text-gray-800 dark:text-white"
                    style="background-color: {{ $appointment->booking_code }}30">
                    {{ $appointment->booking_code }}
                </span>
            @endscope

            @scope('cell.date', $appointment)
                {{ $appointment->date ? \Carbon\Carbon::parse($appointment->date)->format('M d, Y') : 'N/A' }}
            @endscope

            <!-- Expandable row details -->
            @scope('expansion', $appointment)
                <div class="bg-base-200 dark:bg-zinc-700 rounded-lg overflow-hidden">

                    <!-- Status Change Section at the top -->
                    <div class="bg-base-300 dark:bg-zinc-600 p-4 border-b border-base-content/10">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Change Status:</span>

                            @if (in_array($appointment->status, ['completed', 'cancelled']))
                                <x-mary-badge class="badge badge-warning font-medium"
                                    value="{{ ucfirst($appointment->status) }} and cannot be edited." />
                            @else
                                <x-mary-select :options="[
                                    ['id' => 'confirmed', 'name' => 'Confirmed'],
                                    ['id' => 'completed', 'name' => 'Completed'],
                                    ['id' => 'cancelled', 'name' => 'Cancelled'],
                                ]"
                                    wire:change="updateStatus({{ $appointment->id }}, $event.target.value)"
                                    wire:model="currentStatus.{{ $appointment->id }}"
                                    class="w-full sm:w-48 dark:bg-zinc-800" />
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">

                        <!-- Left Column -->
                        <div class="space-y-3">
                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Booking
                                    Code</span>
                                <div class="mt-1">
                                    <span class="px-2 py-1 rounded-lg font-bold text-gray-800 dark:text-white text-sm"
                                        style="background-color: {{ $appointment->booking_code }}30">
                                        {{ $appointment->booking_code }}
                                    </span>
                                </div>
                            </div>

                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</span>
                                <div class="mt-1">
                                    <div @class([
                                        'badge font-medium inline-flex items-center gap-1',
                                        'badge-success text-success-content' =>
                                            $appointment->status === 'confirmed',
                                        'badge-error text-error-content' => $appointment->status === 'cancelled',
                                        'badge-info text-info-content' => $appointment->status === 'completed',
                                        'badge-ghost' => !in_array($appointment->status, [
                                            'confirmed',
                                            'cancelled',
                                            'completed',
                                        ]),
                                    ])>
                                        @switch($appointment->status)
                                            @case('confirmed')
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4a1 1 0 00-1.414-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            @break

                                            @case('cancelled')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            @break

                                            @case('completed')
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            @break
                                        @endswitch
                                        {{ Str::title($appointment->status) }}
                                    </div>
                                </div>
                            </div>

                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Customer
                                    Name</span>
                                <p class="mt-1 text-sm font-medium">{{ $appointment->customer_name ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Email</span>
                                <p class="mt-1 text-sm">{{ $appointment->customer_email ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Phone</span>
                                <p class="mt-1 text-sm">{{ $appointment->customer_phone ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Notes</span>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                    {{ $appointment->notes ?? 'No notes' }}</p>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-3">
                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Appointment
                                    Date</span>
                                <p class="mt-1 text-sm">
                                    {{ $appointment->date ? \Carbon\Carbon::parse($appointment->date)->format('M d, Y') : 'N/A' }}
                                </p>
                            </div>

                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Appointment
                                    Time</span>
                                <p class="mt-1 text-sm">{{ $appointment->start_time ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Branch</span>
                                <p class="mt-1 text-sm">{{ $appointment->branch->name ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <span
                                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Service</span>
                                <p class="mt-1 text-sm">{{ $appointment->service->name ?? 'N/A' }}</p>
                            </div>

                            <div>
                                <span
                                    class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Created</span>
                                <p class="mt-1 text-sm">{{ $appointment->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endscope

        </x-mary-table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $appointments->links() }}
        </div>

        @if ($appointments->isEmpty() && $searchInput)
            <div class="text-center text-gray-500 py-6">
                No appointment match your search "{{ $searchInput }}".
            </div>
        @endif

    </div>
</x-mary-card>
