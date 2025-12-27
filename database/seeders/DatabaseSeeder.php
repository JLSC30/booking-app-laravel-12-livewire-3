<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Service;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create(
            [
                'name' => 'Jan Doe',
                'email' => 'jan@doe.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_admin' => true,
                'remember_token' => Str::random(10),
                'designation' => 'Administrator',
                'must_change_password' => false,
            ],
        );

        User::create(
            [
                'name' => 'Jane Doe',
                'email' => 'jane@doe.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_admin' => false,
                'remember_token' => Str::random(10),
                'designation' => 'Staff',
                'must_change_password' => true,
            ],
        );

        Branch::create(
            [
                'name' => 'Main Branch',
                'address' => '123 Main St, Cityville',
                'is_active' => true,
            ]
        );

        Branch::create(
            [
                'name' => 'Secondary Branch',
                'address' => '456 Side St, Townsville',
                'is_active' => true,
            ]
        );

        Service::create(
            [
                'name' => 'Main Service',
                'description' => 'Main service description',
                'price' => 100.00,
                'duration' => 60,
            ]
        );

        Service::create(
            [
                'name' => 'Second Service',
                'description' => 'Second service description',
                'price' => 150.00,
                'duration' => 90,
            ]
        );

        $this->call(BranchScheduleSeeder::class);
    }
}
