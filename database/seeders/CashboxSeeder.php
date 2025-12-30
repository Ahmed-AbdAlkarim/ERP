<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cashbox;

class CashboxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run() {
        $daily = Cashbox::firstOrCreate(['name' => 'daily_safe']);
        $main  = Cashbox::firstOrCreate(['name' => 'main_safe']);

        $types = ['cash', 'visa', 'vodafone'];

        foreach([$daily, $main] as $cashbox){
            foreach($types as $type){
                $cashbox->wallets()->firstOrCreate(['type' => $type]);
            }
        }
    }

}
