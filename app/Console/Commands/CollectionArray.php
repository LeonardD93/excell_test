<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CollectionArray extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:collection-array';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $employees = [
            ['name' => 'John', 'city' => 'Dallas'],
            ['name' => 'Jane', 'city' => 'Austin'],
            ['name' => 'Jake', 'city' => 'Dallas'],
            ['name' => 'Jill', 'city' => 'Dallas'],
        ];

        $offices = [
            ['office' => 'Dallas HQ', 'city' => 'Dallas'],
            ['office' => 'Dallas South', 'city' => 'Dallas'],
            ['office' => 'Austin Branch', 'city' => 'Austin'],
        ];

        // $output = [
        //     "Dallas" => [
        //         "Dallas HQ" => ["John", "Jake", "Jill"],
        //         "Dallas South" => ["John", "Jake", "Jill"],
        //     ],
        //     "Austin" => [
        //         "Austin Branch" => ["Jane"],
        //     ],
        // ];

        $output2 = collect($offices)
        ->groupBy('city')
        ->mapWithKeys(function ($offices, $city) use ($employees) {
            return [
                $city => $offices->mapWithKeys(function ($office) use ($employees, $city) {
                $officeEmployees = collect($employees)->where('city', $city)->pluck('name')->toArray();
                return [$office['office'] => $officeEmployees];
            })];
        })
        ->toArray();

        print_r($output2);
    }
}
