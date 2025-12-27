<div class="max-w-2xl mx-auto mt-20 text-center">
    @if ($message && !$showConfirmation && !$cancelled)
        <h1 class="text-3xl font-bold text-red-600">Cancellation Not Possible</h1>
        <p class="mt-6 text-lg">{{ $message }}</p>
    @elseif($showConfirmation)
        <h1 class="text-3xl font-bold text-orange-600">Cancel Your Appointment?</h1>
        <p class="mt-6 text-lg">Please confirm if you want to cancel the following appointment:</p>

        <x-mary-card class="bg-base-200 dark:bg-zinc-800 mt-2" shadow>
            <p><strong>Date:</strong> {{ $appointment->date->format('F j, Y') }}</p>
            <p><strong>Time:</strong> {{ $appointment->start_time }} - {{ $appointment->end_time }}</p>
            <p><strong>Service:</strong> {{ $appointment->service->name ?? 'N/A' }}</p>
            <p><strong>Branch:</strong> {{ $appointment->branch->name ?? 'N/A' }}</p>
        </x-mary-card>

        <div class="mt-10 space-x-4">
            <x-mary-button wire:click="confirmCancellation" wire:loading.attr="disabled" class="btn-error text-white">
                Yes, Cancel Appointment
            </x-mary-button>
            <x-mary-button link="{{ url('/') }}" class="btn-outline">
                No, Keep Appointment
            </x-mary-button>
        </div>
    @elseif($cancelled)
        <h1 class="text-3xl font-bold text-green-600">Appointment Cancelled</h1>
        <p class="mt-6 text-lg">{{ $message }}</p>
        <div class="mt-8">
            <p><strong>Date:</strong> {{ $appointment->date->format('F j, Y') }}</p>
            <p><strong>Time:</strong> {{ $appointment->start_time }} - {{ $appointment->end_time }}</p>
        </div>
    @endif

    {{-- Show "Back to Home" only when NOT in confirmation step --}}
    @if (!$showConfirmation)
        <x-mary-button link="{{ url('/') }}" class="btn-neutral dark:btn-secondary mt-10">
            Back to Home
        </x-mary-button>
    @endif
</div>
