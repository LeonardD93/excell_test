<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Faker\Factory as Faker;

class SpreadSheetServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        // this should only test if the validator works properly, need an other thest to test ProcessProductImage::dispatch job
        $service = new \App\Services\SpreadsheetService();
        $faker = Faker::create();
        $data = [['product_code', 'quantity']];

        for($i = 0; $i < 10; $i++) {
            $data[] = [
                'product_code' => Product::factory()->code,
                'quantity' => $faker->numberBetween(1, 20),
            ];
        }
        
        $path  = storage_path('excel'. DIRECTORY_SEPARATOR . 'test.csv');
        $file = fopen($path, 'w');
        foreach ($data as $row) {
            fputcsv($file, $row);
        }
        
        fclose($file);
        $service->processSpreadsheet($path);

        $this->assertTrue(true);
        unlink($path);
    }
}
