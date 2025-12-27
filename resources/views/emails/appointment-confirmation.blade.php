<x-mail::message>
    # Appointment Confirmed!

    Hello **{{ $appointment->customer_name }}**,

    Your appointment has been successfully booked.

    **Details:**
    - **Date:** {{ $appointment->formatted_date }}
    - **Time:** {{ $appointment->formatted_time }}
    - **Service:** {{ $appointment->service->name ?? 'N/A' }}
    - **Branch:** {{ $appointment->branch->name ?? 'N/A' }}

    @if ($appointment->notes)
        **Notes:** {{ $appointment->notes }}
    @endif

    ### Need to cancel?
    @if ($cancelUrl)
        You can cancel your appointment **up until the day before your scheduled date** using this secure link:

        <x-mail::button :url="$cancelUrl">
            Cancel Appointment
        </x-mail::button>

        *This link will expire at the end of {{ $canCancelUntil }}.*
    @else
        Please note: Your appointment is too soon. Same-day cancellations are not allowed through this link. Contact us
        directly if needed.
    @endif

    Thank you for choosing us!
    We look forward to seeing you.

    Best regards,
    Booking App
</x-mail::message>
