<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ImportUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:import {csv_file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bulk import users from a CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('csv_file');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $handle = fopen($file, 'r');
        $header = fgetcsv($handle); // Skip header

        $count = 0;
        $errors = 0;

        while (($data = fgetcsv($handle)) !== false) {
            try {
                // name, email, role, department
                $name = $data[0] ?? null;
                $email = $data[1] ?? null;
                $role = $data[2] ?? 'user';
                $department = $data[3] ?? null;

                if (!$name || !$email) {
                    $this->warn("Skipping invalid row: " . implode(',', $data));
                    $errors++;
                    continue;
                }

                User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'password' => Hash::make(Str::random(16)),
                        'role' => $role,
                        'department' => $department,
                    ]
                );

                $count++;
            } catch (\Exception $e) {
                $this->error("Error importing user {$email}: " . $e->getMessage());
                $errors++;
            }
        }

        fclose($handle);

        $this->info("Successfully imported {$count} users.");
        if ($errors > 0) {
            $this->warn("Encountered {$errors} errors during import.");
        }

        return 0;
    }
}
