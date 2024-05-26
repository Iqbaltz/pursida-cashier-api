<?php

namespace Database\Seeders;

use App\Models\StoreInformation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoreInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('store_information')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        StoreInformation::insert([
            'type' => 'store_data',
            'name' => 'UD. PURSIDA',
            'address' => 'Jl. Asahan Km. VI, Depan',
            'phone_number' => '087776827032'
        ]);
    }
}
