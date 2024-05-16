<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('payment_methods')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Category::insert([
            [
                'slug' => 'tunai',
                'name' => 'Tunai',
            ],
            [
                'slug' => 'ovo',
                'name' => 'OVO',
            ],
            [
                'slug' => 'gopay',
                'name' => 'Gopay',
            ],
            [
                'slug' => 'shopee-pay',
                'name' => 'Shopee Pay',
            ],
            [
                'slug' => 'debit-bca',
                'name' => 'Debit BCA',
            ],
            [
                'slug' => 'debit-mandiri',
                'name' => 'Debit Mandiri',
            ],
            [
                'slug' => 'debit-bri',
                'name' => 'Debit BRI',
            ],
            [
                'slug' => 'debit-bni',
                'name' => 'Debit BNI',
            ],
        ]);
    }
}
