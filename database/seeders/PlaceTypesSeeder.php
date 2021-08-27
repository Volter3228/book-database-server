<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlaceTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('place_types')->insert([
            'name' => 'shelf',
        ]);
        DB::table('place_types')->insert([
            'name' => 'box',
        ]);
    }
}
