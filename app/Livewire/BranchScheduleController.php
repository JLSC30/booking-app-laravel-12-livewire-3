<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\BranchSchedule;
use Livewire\Component;
use Mary\Traits\Toast;

class BranchScheduleController extends Component
{
    use Toast;

    public $branches;

    public $selectedBranchId;

    public $schedules = []; // Array of schedules indexed by day_of_week (1-7)

    public $days = [
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
        7 => 'Sunday',
    ];

    protected $rules = [
        'schedules.*.start_time' => 'required_if:schedules.*.is_available,true|date_format:H:i',
        'schedules.*.end_time' => 'required_if:schedules.*.is_available,true|date_format:H:i|after:schedules.*.start_time',
        'schedules.*.slot_interval_minutes' => 'required_if:schedules.*.is_available,true|integer|min:15|max:120',
        'schedules.*.is_available' => 'boolean',
    ];

    public function mount()
    {
        // Only admins can access (add your policy check if needed)
        $this->authorize('viewAny', BranchSchedule::class);

        $this->branches = Branch::all();
        if ($this->branches->isNotEmpty()) {
            $this->selectedBranchId = $this->branches->first()->id;
            $this->loadSchedules();
        }
    }

    public function updatedSelectedBranchId()
    {
        $this->loadSchedules();
    }

    public function loadSchedules()
    {
        // Load existing as pure arrays
        $existing = BranchSchedule::where('branch_id', $this->selectedBranchId)
            ->get()
            ->mapWithKeys(function ($schedule) {
                return [$schedule->day_of_week => $schedule->toArray()];
            })
            ->toArray();

        // Build full week with defaults where missing
        $this->schedules = [];
        for ($day = 1; $day <= 7; $day++) {
            $this->schedules[$day] = $existing[$day] ?? [
                'branch_id' => $this->selectedBranchId,
                'day_of_week' => $day,
                'start_time' => '09:00',
                'end_time' => '17:00',
                'slot_interval_minutes' => 60,
                'is_available' => true,
            ];
        }

        ksort($this->schedules);
    }

    public function save()
    {
        foreach ($this->schedules as $day => $data) {
            if ($data['is_available']) {
                $this->validate([
                    "schedules.{$day}.start_time" => 'required|date_format:H:i',
                    "schedules.{$day}.end_time" => 'required|date_format:H:i|after:schedules.'.$day.'.start_time',
                    "schedules.{$day}.slot_interval_minutes" => 'required|integer|min:15|max:120',
                ]);
            }
        }

        foreach ($this->schedules as $scheduleData) {
            BranchSchedule::updateOrCreate(
                ['branch_id' => $this->selectedBranchId, 'day_of_week' => $scheduleData['day_of_week']],
                $scheduleData
            );
        }

        $this->success('Branch schedule saved successfully!');
    }

    public function render()
    {
        return view('livewire.branch-schedule.branch-schedule-index');
    }
}
