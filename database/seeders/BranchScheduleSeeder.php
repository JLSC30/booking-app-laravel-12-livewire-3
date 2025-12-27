<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchSchedule;
use Illuminate\Database\Seeder;

class BranchScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all branches
        $branches = Branch::all();

        if ($branches->isEmpty()) {
            $this->command->warn('No branches found. Skipping BranchSchedule seeding.');

            return;
        }

        // Define default weekly schedule template
        $defaultSchedule = [
            1 => [ // Monday
                'start_time' => '09:00',
                'end_time' => '18:00',
                'slot_interval_minutes' => 60,
                'is_available' => true,
            ],
            2 => [ // Tuesday
                'start_time' => '09:00',
                'end_time' => '18:00',
                'slot_interval_minutes' => 60,
                'is_available' => true,
            ],
            3 => [ // Wednesday
                'start_time' => '09:00',
                'end_time' => '18:00',
                'slot_interval_minutes' => 60,
                'is_available' => true,
            ],
            4 => [ // Thursday
                'start_time' => '09:00',
                'end_time' => '18:00',
                'slot_interval_minutes' => 60,
                'is_available' => true,
            ],
            5 => [ // Friday
                'start_time' => '09:00',
                'end_time' => '18:00',
                'slot_interval_minutes' => 60,
                'is_available' => true,
            ],
            6 => [ // Saturday
                'start_time' => '10:00',
                'end_time' => '14:00',
                'slot_interval_minutes' => 60,
                'is_available' => true,
            ],
            7 => [ // Sunday
                'start_time' => null,
                'end_time' => null,
                'slot_interval_minutes' => 60,
                'is_available' => false,
            ],
        ];

        foreach ($branches as $branch) {
            $this->command->info("Seeding schedule for branch: {$branch->name}");

            foreach ($defaultSchedule as $dayOfWeek => $data) {
                BranchSchedule::updateOrCreate(
                    [
                        'branch_id' => $branch->id,
                        'day_of_week' => $dayOfWeek,
                    ],
                    [
                        'start_time' => $data['is_available'] ? $data['start_time'] : null,
                        'end_time' => $data['is_available'] ? $data['end_time'] : null,
                        'slot_interval_minutes' => $data['slot_interval_minutes'],
                        'is_available' => $data['is_available'],
                    ]
                );
            }
        }

        $this->command->info('Branch schedules seeded successfully for all branches!');
    }
}
