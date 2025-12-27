<?php

namespace App\Livewire\Booking;

use App\Mail\AppointmentConfirmation;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Service;
use App\Services\AppointmentSlotService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Mary\Traits\Toast;

class Booking extends Component
{
    use Toast;

    public $branchId = '';

    public $serviceId = '';

    public $date = '';

    public $selectedTime = null;

    public $customer_name = '';

    public $customer_email = '';

    public $customer_phone = '';

    public $notes = '';

    public array $branches = [];

    public array $services = [];

    public array $availableSlots = [];

    public int $step = 1;

    public array $fullyBookedDates = [];

    public function mount()
    {
        $this->branches = Branch::all()
            ->map(fn ($b) => ['id' => $b->id, 'name' => $b->name])
            ->values()
            ->all(); // Clean plain array

        $this->services = Service::all()
            ->map(fn ($s) => ['id' => $s->id, 'name' => $s->name, 'duration' => $s->duration])
            ->values()
            ->all(); // Clean plain array

        $this->updateFullyBookedDates();
    }

    public function updatedBranchId()
    {
        $this->date = '';
        $this->selectedTime = null;
        $this->availableSlots = [];

        $this->updateFullyBookedDates();
    }

    private function updateFullyBookedDates()
    {
        if (! $this->branchId) {
            $this->fullyBookedDates = [];
            $this->dispatch('update-disabled-dates', []);

            return;
        }

        $start = now()->addDay();
        $end = $start->copy()->addDays(90);
        $dailyLimit = (int) config('app.daily_booking_limit');

        $this->fullyBookedDates = Appointment::query()
            ->where('branch_id', $this->branchId)
            ->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->where('status', 'confirmed')
            ->selectRaw('date, COUNT(*) as booking_count')
            ->groupBy('date')
            ->having('booking_count >= ', [$dailyLimit])
            ->pluck('date')
            ->toArray();

        $this->dispatch('update-disabled-dates', $this->fullyBookedDates);
    }

    public function updatedDate($value)
    {
        $this->selectedTime = null;
        $this->availableSlots = [];

        if (strlen($value) === 10 && $this->branchId && $this->serviceId) {
            $this->loadSlots();
        }
    }

    public function loadSlots()
    {
        $slotService = new AppointmentSlotService;
        $slots = $slotService->getAvailableSlots((int) $this->branchId, (int) $this->serviceId, $this->date);

        // Force clean indexed array of strings
        $this->availableSlots = array_values(array_map('strval', $slots));
        $this->selectedTime = null;
    }

    public function prevStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function nextStep()
    {
        if ($this->step == 1 && $this->branchId && $this->serviceId) {
            $this->step = 2;
        } elseif ($this->step == 2 && $this->date && $this->selectedTime) {
            $this->step = 3;
        }
    }

    private function generateBookingCode(): string
    {
        // Generate light, soft colors by keeping RGB values high (180–240 range)
        $r = mt_rand(180, 240);
        $g = mt_rand(180, 240);
        $b = mt_rand(180, 240);

        // Optional: Add slight tint variation for more diversity
        $variation = mt_rand(0, 2);
        if ($variation === 0) {
            $r += mt_rand(10, 30);
        }     // warmer
        if ($variation === 1) {
            $g += mt_rand(10, 30);
        }     // greener
        if ($variation === 2) {
            $b += mt_rand(10, 30);
        }     // cooler

        // Clamp to valid range
        $r = min(255, $r);
        $g = min(255, $g);
        $b = min(255, $b);

        $hex = sprintf('#%02X%02X%02X', $r, $g, $b);

        // Ensure uniqueness
        if (Appointment::where('booking_code', $hex)->exists()) {
            return $this->generatePastelBookingCode(); // recurse until unique
        }

        return $hex;
    }

    public function book()
    {
        $hourlyLimit = (int) config('app.hourly_booking_limit');
        $this->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $currentBookingsAtTime = Appointment::where('branch_id', $this->branchId)
            ->where('date', $this->date)
            ->where('start_time', $this->selectedTime)
            ->where('status', 'confirmed')
            ->count();

        if ($currentBookingsAtTime >= $hourlyLimit) {
            $this->error('Sorry, this time slot is fully booked. Please choose another time.');

            return;
        }

        $service = Service::findOrFail($this->serviceId);

        $endTime = \Carbon\Carbon::createFromFormat('H:i', $this->selectedTime)
            ->addMinutes($service->duration)
            ->format('H:i');

        $appointment = Appointment::create([
            'branch_id' => $this->branchId,
            'service_id' => $this->serviceId,
            'date' => $this->date,
            'start_time' => $this->selectedTime,
            'end_time' => $endTime,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'notes' => $this->notes,
            'status' => 'confirmed',
            'token' => Str::random(60),
            'booking_code' => $this->generateBookingCode(),
        ]);

        $this->success('Appointment request sent! We will confirm soon.');
        Mail::to($appointment->customer_email)->send(
            new AppointmentConfirmation($appointment)
        );

        $this->reset([
            'branchId', 'serviceId', 'date', 'selectedTime',
            'customer_name', 'customer_email', 'customer_phone', 'notes',
            'availableSlots', 'step',
        ]);
        $this->step = 1;
    }

    public function render()
    {
        return view('livewire.booking.booking')
            ->layout('components.layouts.guest');
    }
}
