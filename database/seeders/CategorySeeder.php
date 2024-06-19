<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Category::insert([
            [
                'name' => 'Seng',
            ],
            [
                'name' => 'Keramik',
            ],
            [
                'name' => 'Cat',
            ],
            [
                'name' => 'Broti',
            ],
            [
                'name' => 'Triplek',
            ],
            [
                'name' => 'Thinner',
            ],
            [
                'name' => 'Calsibot',
            ],
            [
                'name' => 'Papan Gipsum',
            ],
            [
                'name' => 'Pintu',
            ],
            [
                'name' => 'Asbes',
            ],
            [
                'name' => 'Lem',
            ],
            [
                'name' => 'Lem Pipa PVC',
            ],
            [
                'name' => 'Senter',
            ],
            [
                'name' => 'Lampu',
            ],
            [
                'name' => 'Saklar',
            ],
            [
                'name' => 'Terminal',
            ],
            [
                'name' => 'Mangkok PNS',
            ],
            [
                'name' => 'Fitting',
            ],
            [
                'name' => 'Steker',
            ],
            [
                'name' => 'Stop Kontak',
            ],
            [
                'name' => 'Lat Asbes',
            ],
            [
                'name' => 'Siku Rak',
            ],
            [
                'name' => 'T',
            ],
            [
                'name' => 'Elbow',
            ],
            [
                'name' => 'Sambungan',
            ],
            [
                'name' => 'DOP',
            ],
            [
                'name' => 'Meteran',
            ],
            [
                'name' => 'Kuas',
            ],
            [
                'name' => 'Mata Bor',
            ],
            [
                'name' => 'Soket',
            ],
            [
                'name' => 'Kran',
            ],
            [
                'name' => 'Double Neple',
            ],
            [
                'name' => 'R-Sok',
            ],
            [
                'name' => 'Selang',
            ],
            [
                'name' => 'Shower',
            ],
            [
                'name' => 'Kepala Kunci',
            ],
            [
                'name' => 'Boyo',
            ],
            [
                'name' => 'Waterpass',
            ],
            [
                'name' => 'Tape',
            ],
            [
                'name' => 'Kain Kasa',
            ],
            [
                'name' => 'Kakak Tua',
            ],
            [
                'name' => 'Gagang Pintu',
            ],
            [
                'name' => 'Pahat',
            ],
            [
                'name' => 'Kabel',
            ],
        ]);
    }
}
