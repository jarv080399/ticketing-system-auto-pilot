<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $holidays = [
            // Standard Global Holidays (Recurring)
            ['name' => 'New Year\'s Day', 'date' => '2026-01-01', 'is_recurring' => true],
            ['name' => 'Labor Day', 'date' => '2026-05-01', 'is_recurring' => true],
            ['name' => 'Christmas Day', 'date' => '2026-12-25', 'is_recurring' => true],
            ['name' => 'Rizal Day', 'date' => '2026-12-30', 'is_recurring' => true],
            ['name' => 'New Year\'s Eve', 'date' => '2026-12-31', 'is_recurring' => true],
            
            // Philippine Specific Holidays (Movable/Non-Recurring for 2026)
            ['name' => 'Chinese New Year', 'date' => '2026-02-17', 'is_recurring' => false],
            ['name' => 'EDSA People Power Revolution Anniversary', 'date' => '2026-02-25', 'is_recurring' => false],
            ['name' => 'Araw ng Kagitingan (Day of Valor)', 'date' => '2026-04-09', 'is_recurring' => false],
            ['name' => 'Maundy Thursday', 'date' => '2026-04-02', 'is_recurring' => false],
            ['name' => 'Good Friday', 'date' => '2026-04-03', 'is_recurring' => false],
            ['name' => 'Black Saturday', 'date' => '2026-04-04', 'is_recurring' => false],
            ['name' => 'Independence Day', 'date' => '2026-06-12', 'is_recurring' => true], // Fixed Date
            ['name' => 'Ninoy Aquino Day', 'date' => '2026-08-21', 'is_recurring' => true], // Fixed Date
            ['name' => 'National Heroes Day', 'date' => '2026-08-31', 'is_recurring' => false], // Usually last monday of August
            ['name' => 'All Saints\' Day', 'date' => '2026-11-01', 'is_recurring' => true], // Fixed Date
            ['name' => 'All Souls\' Day', 'date' => '2026-11-02', 'is_recurring' => true], // Fixed Date
            ['name' => 'Bonifacio Day', 'date' => '2026-11-30', 'is_recurring' => true], // Fixed Date
            ['name' => 'Feast of the Immaculate Conception of Mary', 'date' => '2026-12-08', 'is_recurring' => true], // Fixed Date
        ];

        foreach ($holidays as $holiday) {
            Holiday::firstOrCreate(
                ['name' => $holiday['name'], 'date' => $holiday['date']],
                $holiday
            );
        }
    }
}
