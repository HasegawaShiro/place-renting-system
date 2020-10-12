<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Places;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Places::insert([
            ['place_name' => '測試場地1', 'place_disabled' => false],
            ['place_name' => '測試場地2', 'place_disabled' => false],
            ['place_name' => '測試場地3', 'place_disabled' => false],
            ['place_name' => '測試場地4', 'place_disabled' => false],
            ['place_name' => '測試場地5', 'place_disabled' => true],
        ]);
    }
}
