<div>
    <x-mary-header title="Manage Branch Schedules" subtitle="Set weekly opening hours for each branch" />

    <x-mary-card class="bg-base-200 dark:bg-zinc-800" shadow>
        <div class="mb-6">
            <flux:select wire:model.live="selectedBranchId" label="Select Branch" placeholder="Choose a branch..."
                class="w-full !ring-black focus:!ring-black dark:!ring-white dark:focus:!ring-white">

                @foreach ($branches as $branch)
                    <flux:select.option value="{{ $branch->id }}" wire:key="branch-{{ $branch->id }}">
                        {{ $branch->name }}
                    </flux:select.option>
                @endforeach

            </flux:select>
        </div>

        @if ($selectedBranchId)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($days as $dayNum => $dayName)
                    <x-mary-card class="bg-base-200 dark:bg-zinc-800" shadow>
                        <h3 class="text-lg font-semibold mb-4">{{ $dayName }}</h3>

                        <x-mary-toggle label="Available this day"
                            wire:model.live="schedules.{{ $dayNum }}.is_available" class="toggle-secondary" />

                        <div class="{{ $schedules[$dayNum]['is_available'] ? '' : 'opacity-50' }}">
                            <div class="grid grid-cols-1 gap-4 my-4">
                                <x-mary-input type="time" label="Start Time"
                                    wire:model.live="schedules.{{ $dayNum }}.start_time" class="dark:bg-zinc-800"
                                    :disabled="!$schedules[$dayNum]['is_available']" icon="o-clock" inline />

                                <x-mary-input type="time" label="End Time"
                                    wire:model.live="schedules.{{ $dayNum }}.end_time" class="dark:bg-zinc-800"
                                    :disabled="!$schedules[$dayNum]['is_available']" icon="o-clock" inline />
                            </div>

                            <x-mary-input type="number" label="Slot Interval (minutes)" min="60" max="120"
                                step="10" wire:model.live="schedules.{{ $dayNum }}.slot_interval_minutes"
                                :disabled="!$schedules[$dayNum]['is_available']" inline class="dark:bg-zinc-800" />
                        </div>
                    </x-mary-card>
                @endforeach
            </div>

            <div class="mt-8 flex justify-end">
                <x-mary-button class="btn-neutral dark:btn-secondary!" wire:click="save">
                    {{ __('Save All Schedules') }}
                </x-mary-button>
            </div>
        @else
            <div class="text-center py-10 text-gray-500">
                No branches found. Create one first!
            </div>
        @endif
    </x-mary-card>
</div>
