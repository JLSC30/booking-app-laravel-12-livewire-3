<?php

namespace App\Livewire\Booking;

use App\Models\Appointment;
use Livewire\Component;

class Cancel extends Component
{
    public string $token = '';

    public ?Appointment $appointment = null;

    public string $message = '';

    public bool $showConfirmation = false;  // New: show details + button

    public bool $cancelled = false;

    public function mount($token)
    {
        $this->token = $token;

        if (! request()->hasValidSignature()) {
            $this->message = 'This cancellation link is invalid or has expired.';

            return;
        }

        $this->appointment = Appointment::where('token', $token)->firstOrFail();

        if ($this->appointment->status === 'cancelled') {
            $this->message = 'This appointment has already been cancelled.';

            return;
        }

        // Do NOT cancel here anymore
        // Only show the details and button if still cancellable
        if ($this->appointment->date->isPast()) {
            $this->message = 'Sorry, past appointments cannot be cancelled.';

            return;
        }

        $this->showConfirmation = true;  // Show the "Are you sure?" form
    }

    public function confirmCancellation()
    {
        // Double-check everything again (security)
        if ($this->appointment->status === 'cancelled') {
            $this->message = 'This appointment has already been cancelled.';

            return;
        }

        if ($this->appointment->date->isPast()) {
            $this->message = 'Sorry, this appointment is in the past and cannot be cancelled.';

            return;
        }

        $this->appointment->update([
            'status' => 'cancelled',
            'token' => null, // Prevent reuse
        ]);

        $this->cancelled = true;
        $this->showConfirmation = false;
        $this->message = 'Your appointment has been successfully cancelled.';
    }

    public function render()
    {
        return view('livewire.booking.cancel')->layout('components.layouts.guest');
    }
}
