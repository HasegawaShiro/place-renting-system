<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Place;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Place::insert([
            ['place_code' => 'a', 'place_name' => '測試場地1', 'place_disabled' => false],
            ['place_code' => 'b', 'place_name' => '測試場地2', 'place_disabled' => false],
            ['place_code' => 'c', 'place_name' => '測試場地3', 'place_disabled' => false],
            ['place_code' => 'd', 'place_name' => '測試場地4', 'place_disabled' => false],
            ['place_code' => 'e', 'place_name' => '測試場地5', 'place_disabled' => true],
        ]);
    }
}
