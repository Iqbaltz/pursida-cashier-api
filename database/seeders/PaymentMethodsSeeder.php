<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\PaymentMethods;
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
        PaymentMethods::insert([
            [
                'name' => 'Tunai',
            ],
            [
                'name' => 'OVO',
            ],
            [
                'name' => 'Gopay',
            ],
            [
                'name' => 'Shopee Pay',
            ],
            [
                'name' => 'Debit BCA',
            ],
            [
                'name' => 'Debit Mandiri',
            ],
            [
                'name' => 'Debit BRI',
            ],
            [
                'name' => 'Debit BNI',
            ],
        ]);
    }
}
