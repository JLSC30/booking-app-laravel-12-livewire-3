<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $service = Service::inRandomOrder()->first() ?? Service::factory()->create();
        $startTime = $this->faker->time('H:i');
        $endTime = \Carbon\Carbon::createFromFormat('H:i', $startTime)
            ->addMinutes($service->duration)
            ->format('H:i');

        return [
            'branch_id' => Branch::factory(),
            'service_id' => $service,
            'date' => $this->faker->dateTimeBetween('+1 day', '+30 days')->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'customer_name' => $this->faker->name,
            'customer_email' => $this->faker->unique()->safeEmail,
            'customer_phone' => $this->faker->numerify('09#########'),
            'booking_code' => '#TEST'.$this->faker->unique()->numberBetween(100, 999),
            'status' => 'confirmed',
            'notes' => $this->faker->optional(0.7)->sentence,
            'token' => Str::random(60), // ← THIS IS REQUIRED
        ];
    }

    public function confirmed()
    {
        return $this->state(['status' => 'confirmed']);
    }

    public function completed()
    {
        return $this->state(['status' => 'completed']);
    }

    public function cancelled()
    {
        return $this->state(['status' => 'cancelled']);
    }
}
