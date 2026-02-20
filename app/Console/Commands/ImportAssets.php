<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\AssetHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ImportAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:assets {file : The path to the CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import assets from a CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: $filePath");
            return 1;
        }

        $file = fopen($filePath, 'r');
        $header = fgetcsv($file);

        if (!$header) {
            $this->error("Invalid or empty CSV file.");
            return 1;
        }

        $rowCount = 0;
        $successCount = 0;
        $errorCount = 0;

        $this->info("Starting import...");
        $bar = $this->output->createProgressBar();
        $bar->start();

        DB::beginTransaction();

        try {
            while (($row = fgetcsv($file)) !== false) {
                $data = array_combine($header, $row);
                
                $validator = Validator::make($data, [
                    'serial_number' => 'required|string|unique:assets,serial_number',
                    'type' => 'required|in:laptop,desktop,monitor,printer,phone,license,peripheral,other',
                    'manufacturer' => 'nullable|string',
                    'model' => 'nullable|string',
                    'status' => 'nullable|in:active,in_repair,retired,disposed,in_stock',
                ]);

                if ($validator->fails()) {
                    $this->warn("\nSkipping row " . ($rowCount + 1) . ": " . implode(', ', $validator->errors()->all()));
                    $errorCount++;
                    continue;
                }

                $asset = Asset::create($data);
                
                AssetHistory::create([
                    'asset_id' => $asset->id,
                    'action' => 'created',
                    'performed_by' => null, // Systems import
                    'new_value' => 'Imported via CSV',
                ]);

                $successCount++;
                $rowCount++;
                $bar->advance();

                if ($rowCount % 500 === 0) {
                    DB::commit();
                    DB::beginTransaction();
                }
            }

            DB::commit();
            $bar->finish();
            $this->info("\n\nImport completed!");
            $this->info("Successfully imported: $successCount");
            $this->error("Failed rows: $errorCount");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\nAn error occurred during import: " . $e->getMessage());
            return 1;
        }

        fclose($file);
        return 0;
    }
}
