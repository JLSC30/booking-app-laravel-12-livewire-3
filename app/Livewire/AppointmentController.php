<?php

namespace App\Livewire;

use App\Models\Appointment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

class AppointmentController extends Component
{
    use AuthorizesRequests, Toast, WithPagination;

    public $searchInput = '';

    public $expanded = []; // Required for expandable rows

    public $currentStatus = [];

    protected $updatesQueryString = ['searchInput' => ['except' => '']];

    public function mount()
    {
        $this->authorize('viewAny', Appointment::class);
    }

    public function updatingSearchInput()
    {
        $this->resetPage();
    }

    public function updateStatus($appointmentId, $newStatus)
    {
        try {
            $appointment = Appointment::findOrFail($appointmentId);

            $this->authorize('update', $appointment);  // ← NEW LINE (IMPORTANT)

            $appointment->update(['status' => $newStatus]);

            $this->currentStatus[$appointmentId] = $newStatus;

            $this->success('Status updated successfully to '.ucfirst($newStatus).'!');
        } catch (\Exception $e) {
            $this->error('Failed to update status: '.$e->getMessage());
        }
    }

    public function render()
    {
        $query = Appointment::with(['service', 'branch'])->latest();

        if ($this->searchInput) {
            $query->where('customer_name', 'like', '%'.$this->searchInput.'%')
                ->orWhere('booking_code', 'like', '%'.$this->searchInput.'%')
                ->orWhere('customer_email', 'like', '%'.$this->searchInput.'%');
        }

        $appointments = $query->paginate(10);

        // Initialize currentStatus for each appointment
        foreach ($appointments as $appointment) {
            if (! isset($this->currentStatus[$appointment->id])) {
                $this->currentStatus[$appointment->id] = $appointment->status;
            }
        }

        return view('livewire.appointment.appointment-index', [
            'appointments' => $appointments,
            'headers' => $this->getHeaders(),
        ]);
    }

    private function getHeaders(): array
    {
        return [
            ['key' => 'booking_code', 'label' => 'Code'],
            ['key' => 'customer_name', 'label' => 'Name', 'class' => 'hidden sm:table-cell'],
            ['key' => 'date', 'label' => 'Date'],
            ['key' => 'created_at', 'label' => 'Created', 'class' => 'hidden sm:table-cell'],
        ];
    }
}
