<?php

namespace App\Mail;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class AppointmentConfirmation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $appointment;

    public $cancelUrl;

    public $canCancelUntil;

    /**
     * Create a new message instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;

        // Determine when cancellation is allowed until:
        // End of the day BEFORE the appointment date
        $appointmentDate = Carbon::parse($appointment->date);
        $expiry = $appointmentDate->copy()->subDay()->endOfDay(); // e.g., Dec 27 23:59:59 if appointment is Dec 28

        // If the expiry is already in the past (appointment is today or earlier), no cancellation link
        if ($expiry->isPast()) {
            $this->cancelUrl = null;
            $this->canCancelUntil = null;
        } else {
            $this->cancelUrl = URL::temporarySignedRoute(
                'booking.cancel',           // your route name
                $expiry,
                ['token' => $appointment->token]
            );

            $this->canCancelUntil = $expiry->format('F j, Y'); // e.g., "December 27, 2025"
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Appointment Confirmation - '.$this->appointment->booking_code,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.appointment-confirmation',
            with: [
                'appointment' => $this->appointment,
                'cancelUrl' => $this->cancelUrl,
                'canCancelUntil' => $this->canCancelUntil,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
