<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\BranchSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchScheduleFactory extends Factory
{
    protected $model = BranchSchedule::class;

    public function definition(): array
    {
        // Random day of week: 1 = Monday ... 7 = Sunday
        $dayOfWeek = $this->faker->numberBetween(1, 7);

        // Most days are available with standard hours
        if ($this->faker->boolean(85)) { // 85% chance of being open
            $startTime = $this->faker->randomElement(['08:00', '09:00', '10:00']);
            $endTime = $this->faker->randomElement(['17:00', '18:00', '19:00', '20:00']);

            return [
                'branch_id' => Branch::factory(),
                'day_of_week' => $dayOfWeek,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'slot_interval_minutes' => $this->faker->randomElement([30, 45, 60]),
                'is_available' => true,
            ];
        }

        // Some days are closed
        return [
            'branch_id' => Branch::factory(),
            'day_of_week' => $dayOfWeek,
            'start_time' => null,
            'end_time' => null,
            'slot_interval_minutes' => 30,
            'is_available' => false,
        ];
    }

    /**
     * Indicate that the branch is open on this day.
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => true,
            'start_time' => $this->faker->randomElement(['09:00', '10:00']),
            'end_time' => $this->faker->randomElement(['17:00', '18:00', '19:00']),
            'slot_interval_minutes' => $this->faker->randomElement([30, 60]),
        ]);
    }

    /**
     * Indicate that the branch is closed on this day.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
            'start_time' => null,
            'end_time' => null,
            'slot_interval_minutes' => 30,
        ]);
    }

    /**
     * Set specific working hours.
     */
    public function hours(string $start, string $end, int $interval = 30): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => true,
            'start_time' => $start,
            'end_time' => $end,
            'slot_interval_minutes' => $interval,
        ]);
    }
}
