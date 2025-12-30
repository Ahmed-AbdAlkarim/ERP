<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Safe;
use App\Models\SafeWallet;

class FixedSafesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Daily Safe
        $dailySafe = Safe::firstOrCreate([
            'name' => 'daily_safe',
        ]);

        // Create wallets for Daily Safe
        SafeWallet::firstOrCreate([
            'safe_id' => $dailySafe->id,
            'wallet_type' => 'cash',
            'balance' => 0,
        ]);

        SafeWallet::firstOrCreate([
            'safe_id' => $dailySafe->id,
            'wallet_type' => 'visa',
            'balance' => 0,
        ]);

        SafeWallet::firstOrCreate([
            'safe_id' => $dailySafe->id,
            'wallet_type' => 'vodafone',
            'balance' => 0,
        ]);

        // Create Main Safe
        $mainSafe = Safe::firstOrCreate([
            'name' => 'main_safe',
        ]);

        // Create wallets for Main Safe
        SafeWallet::firstOrCreate([
            'safe_id' => $mainSafe->id,
            'wallet_type' => 'cash',
            'balance' => 0,
        ]);

        SafeWallet::firstOrCreate([
            'safe_id' => $mainSafe->id,
            'wallet_type' => 'visa',
            'balance' => 0,
        ]);

        SafeWallet::firstOrCreate([
            'safe_id' => $mainSafe->id,
            'wallet_type' => 'vodafone',
            'balance' => 0,
        ]);
    }
}
