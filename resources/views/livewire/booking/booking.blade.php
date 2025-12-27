<div class="w-full">
    <div class="text-center mb-6">
        <h1 class="text-3xl font-bold mb-2">Book an Appointment</h1>
        <p class="text-base-content/70">Select your branch, service, and preferred time</p>
    </div>

    <div class="max-w-xl mx-auto px-4">
        <x-mary-card class="bg-base-200 dark:bg-zinc-800" shadow>
            <div class="flex justify-center border-y border-base-content/10 my-5 py-5">
                <x-mary-steps wire:model="step" class="border-y border-base-content/10 my-5 py-5">
                    <x-mary-step step="1" text="Branch & Service">
                        <flux:select wire:model.live="branchId" label="Select Branch" placeholder="Choose a branch..."
                            class="w-full !ring-black focus:!ring-black dark:!ring-white dark:focus:!ring-white mb-4 dark:bg-zinc-800">
                            @foreach ($branches as $branch)
                                <flux:select.option wire:key="branch-{{ $branch['id'] }}" value="{{ $branch['id'] }}">
                                    {{ $branch['name'] }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:select wire:model.live="serviceId" label="Select Service"
                            placeholder="Choose a service..."
                            class="w-full !ring-black focus:!ring-black dark:!ring-white dark:focus:!ring-white dark:bg-zinc-800">
                            @foreach ($services as $service)
                                <flux:select.option wire:key="service-{{ $service['id'] }}"
                                    value="{{ $service['id'] }}">
                                    {{ $service['name'] }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </x-mary-step>

                    <x-mary-step step="2" text="Date & Time">
                        <div class="space-y-10">
                            <div class="max-w-md mx-auto">
                                <div x-data="datePicker()" x-init="init()" wire:ignore>
                                    <label class="label">
                                        <span class="label-text font-medium">Select Date</span>
                                    </label>

                                    <input x-ref="picker" type="text" placeholder="Choose a date..."
                                        class="input input-bordered w-full dark:bg-zinc-800" readonly />

                                    <input type="hidden" wire:model.live="date" x-model="selectedDate" />
                                </div>

                                @if ($date && empty($availableSlots))
                                    <div class="alert alert-warning mt-4 shadow-lg">
                                        <span>No available slots on this date. Please choose another.</span>
                                    </div>
                                @endif
                            </div>

                            @if (strlen($date) === 10 && $branchId && $serviceId)
                                <div class="text-center">
                                    <h3 class="text-xl font-semibold mb-6">
                                        Available Times on
                                        <span class="text-primary">
                                            {{ \Carbon\Carbon::createFromFormat('Y-m-d', $date)->format('l, F j, Y') }}
                                        </span>
                                    </h3>

                                    @if (empty($availableSlots))
                                        <div class="alert alert-warning max-w-md mx-auto shadow-lg">
                                            <span>No available slots on this date.</span>
                                        </div>
                                    @else
                                        <div class="flex flex-wrap justify-center gap-3 max-w-4xl mx-auto">
                                            @foreach ($availableSlots as $slot)
                                                <x-mary-button wire:click="$set('selectedTime', '{{ $slot }}')"
                                                    wire:key="slot-{{ $slot }}"
                                                    class="{{ $selectedTime === $slot ? 'btn-secondary' : 'btn-outline' }} btn-md font-medium px-6 py-3">
                                                    {{ $slot }}
                                                </x-mary-button>
                                            @endforeach
                                        </div>

                                        @if ($selectedTime)
                                            <p class="mt-6 text-success font-medium">
                                                Selected time: <strong>{{ $selectedTime }}</strong>
                                            </p>
                                        @endif
                                    @endif
                                </div>
                            @else
                                <p class="text-center text-base-content/60 text-lg">
                                    Please select a date to view available times
                                </p>
                            @endif
                        </div>

                        {{-- <div class="text-center mt-4 text-xs text-gray-500">
                            Debug: $date = "{{ $date }}"<br>
                            Slots: {{ count($availableSlots) }} available
                        </div> --}}
                    </x-mary-step>

                    <x-mary-step step="3" text="Your Details">
                        <div class="max-w-2xl mx-auto space-y-2">
                            <x-mary-input label="Full Name" wire:model.live="customer_name" placeholder="John Doe"
                                clearable class="dark:bg-zinc-800" />
                            <x-mary-input type="email" label="Email" wire:model.live="customer_email"
                                placeholder="john@example.com" clearable class="dark:bg-zinc-800" />
                            <x-mary-input label="Phone Number" wire:model.live="customer_phone"
                                placeholder="09150001234" type="tel" pattern="[0-9]{11}" minlength="11"
                                maxlength="11" class="dark:bg-zinc-800" clearable
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                            <x-mary-textarea label="Notes (Optional)" wire:model.live="notes"
                                placeholder="Any special requests?" rows="4" class="dark:bg-zinc-800" />
                        </div>
                    </x-mary-step>
                </x-mary-steps>
            </div>

            <div class="flex justify-between mt-6">
                <x-mary-button label="Previous" wire:click="prevStep" @class(['invisible' => $step === 1, 'btn-ghost' => true]) />
                <x-mary-button label="Next" wire:click="nextStep" :disabled="!$branchId || !$serviceId || ($step === 2 && !$date)" @class(['hidden' => $step === 3, 'btn-secondary' => true]) />
                <x-mary-button label="Complete Booking" wire:click="book" :disabled="!$branchId || !$serviceId || !$date || !$selectedTime || $step !== 3" class="btn-primary"
                    @class(['hidden' => $step !== 3, 'btn-secondary' => true]) />
            </div>
        </x-mary-card>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">

    <script>
        function datePicker() {
            return {
                fp: null,
                selectedDate: '{{ $date ?? '' }}',

                init() {
                    this.fp = flatpickr(this.$refs.picker, {
                        dateFormat: "Y-m-d",
                        minDate: "{{ now()->addDay()->format('Y-m-d') }}",
                        disable: @js($fullyBookedDates ?? []),
                        onChange: (selectedDates, dateStr) => {
                            this.selectedDate = dateStr || '';

                            // Force Livewire to notice the change
                            this.$wire.set('date', this.selectedDate);

                            // Clear previous selection
                            this.$wire.set('selectedTime', null);
                        }
                    });

                    // Sync back if Livewire clears/resets the date
                    this.$watch('selectedDate', (value) => {
                        if (value) {
                            this.fp.setDate(value, false);
                        } else {
                            this.fp.clear();
                        }
                    });

                    // Update disabled dates
                    Livewire.on('update-disabled-dates', (dates) => {
                        this.fp.set('disable', dates);
                        this.fp.redraw();
                    });
                }
            }
        }
    </script>
</div>
