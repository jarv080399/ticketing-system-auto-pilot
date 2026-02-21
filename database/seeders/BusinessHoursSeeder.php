<?php

namespace Database\Seeders;

use App\Models\BusinessHour;
use Illuminate\Database\Seeder;

class BusinessHoursSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days = [
            0 => ['name' => 'Sunday', 'is_working' => false],
            1 => ['name' => 'Monday', 'is_working' => true],
            2 => ['name' => 'Tuesday', 'is_working' => true],
            3 => ['name' => 'Wednesday', 'is_working' => true],
            4 => ['name' => 'Thursday', 'is_working' => true],
            5 => ['name' => 'Friday', 'is_working' => true],
            6 => ['name' => 'Saturday', 'is_working' => false],
        ];

        foreach ($days as $dayOfWeek => $info) {
            BusinessHour::updateOrCreate(
                ['day_of_week' => $dayOfWeek],
                [
                    'is_working_day' => $info['is_working'],
                    'start_time' => '09:00:00',
                    'end_time' => '17:00:00',
                ]
            );
        }
    }
}
