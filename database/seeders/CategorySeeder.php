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
                'slug' => 'seng',
                'name' => 'Seng',
            ],
            [
                'slug' => 'keramik',
                'name' => 'Keramik',
            ],
            [
                'slug' => 'cat',
                'name' => 'Cat',
            ],
            [
                'slug' => 'broti',
                'name' => 'Broti',
            ],
            [
                'slug' => 'triplek',
                'name' => 'Triplek',
            ],
            [
                'slug' => 'thinner',
                'name' => 'Thinner',
            ],
            [
                'slug' => 'calsibot',
                'name' => 'Calsibot',
            ],
            [
                'slug' => 'papan-gipsum',
                'name' => 'Papan Gipsum',
            ],
            [
                'slug' => 'pintu',
                'name' => 'Pintu',
            ],
            [
                'slug' => 'asbes',
                'name' => 'Asbes',
            ],
            [
                'slug' => 'lem',
                'name' => 'Lem',
            ],
            [
                'slug' => 'lem-pipa-pvc',
                'name' => 'Lem Pipa PVC',
            ],
            [
                'slug' => 'senter',
                'name' => 'Senter',
            ],
            [
                'slug' => 'lampu',
                'name' => 'Lampu',
            ],
            [
                'slug' => 'saklar',
                'name' => 'Saklar',
            ],
            [
                'slug' => 'terminal',
                'name' => 'Terminal',
            ],
            [
                'slug' => 'mangkok-pns',
                'name' => 'Mangkok PNS',
            ],
            [
                'slug' => 'fitting',
                'name' => 'Fitting',
            ],
            [
                'slug' => 'steker',
                'name' => 'Steker',
            ],
            [
                'slug' => 'stop-kontak',
                'name' => 'Stop Kontak',
            ],
            [
                'slug' => 'lat-asbes',
                'name' => 'Lat Asbes',
            ],
            [
                'slug' => 'siku-rak',
                'name' => 'Siku Rak',
            ],
            [
                'slug' => 't',
                'name' => 'T',
            ],
            [
                'slug' => 'elbow',
                'name' => 'Elbow',
            ],
            [
                'slug' => 'sambungan',
                'name' => 'Sambungan',
            ],
            [
                'slug' => 'dop',
                'name' => 'DOP',
            ],
            [
                'slug' => 'meteran',
                'name' => 'Meteran',
            ],
            [
                'slug' => 'kuas',
                'name' => 'Kuas',
            ],
            [
                'slug' => 'mata-bor',
                'name' => 'Mata Bor',
            ],
            [
                'slug' => 'soket',
                'name' => 'Soket',
            ],
            [
                'slug' => 'kran',
                'name' => 'Kran',
            ],
            [
                'slug' => 'double-neple',
                'name' => 'Double Neple',
            ],
            [
                'slug' => 'r-sok',
                'name' => 'R-Sok',
            ],
            [
                'slug' => 'selang',
                'name' => 'Selang',
            ],
            [
                'slug' => 'shower',
                'name' => 'Shower',
            ],
            [
                'slug' => 'kepala-kunci',
                'name' => 'Kepala Kunci',
            ],
            [
                'slug' => 'boyo',
                'name' => 'Boyo',
            ],
            [
                'slug' => 'waterpass',
                'name' => 'Waterpass',
            ],
            [
                'slug' => 'tape',
                'name' => 'Tape',
            ],
            [
                'slug' => 'kain-kasa',
                'name' => 'Kain Kasa',
            ],
            [
                'slug' => 'kakak-tua',
                'name' => 'Kakak Tua',
            ],
            [
                'slug' => 'gagang-pintu',
                'name' => 'Gagang Pintu',
            ],
            [
                'slug' => 'pahat',
                'name' => 'Pahat',
            ],
            [
                'slug' => 'kabel',
                'name' => 'Kabel',
            ],
        ]);
    }
}
