<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Safe;
use App\Models\SafeWallet;
use App\Models\SafeTransaction;

class TransferDailyCashbox extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:transfer-daily-cashbox';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer all wallet balances from daily safe to main safe at midnight';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dailySafe = Safe::where('name', 'daily')->first();
        $mainSafe = Safe::where('name', 'main')->first();

        if (!$dailySafe || !$mainSafe) {
            $this->error('Safes not found!');
            return;
        }

        $walletTypes = ['cash', 'visa', 'vodafone'];
        $totalTransferred = 0;

        foreach ($walletTypes as $walletType) {
            $dailyWallet = SafeWallet::where('safe_id', $dailySafe->id)
                ->where('wallet_type', $walletType)
                ->first();

            $mainWallet = SafeWallet::where('safe_id', $mainSafe->id)
                ->where('wallet_type', $walletType)
                ->first();

            if (!$dailyWallet || !$mainWallet) {
                $this->warn("Wallet {$walletType} not found in one of the safes");
                continue;
            }

            $amount = $dailyWallet->balance;

            if ($amount <= 0) {
                continue;
            }

            // Transfer from daily to main
            $dailyWallet->decrement('balance', $amount);
            $mainWallet->increment('balance', $amount);

            // Log the transaction
            SafeTransaction::create([
                'safe_id' => $dailySafe->id,
                'wallet_type' => $walletType,
                'amount' => -$amount, // Negative for deduction
                'type' => 'daily_transfer',
                'reference_type' => 'daily_transfer',
                'description' => "Daily transfer of {$walletType} to main safe",
                'user_id' => 1, // System user
            ]);

            SafeTransaction::create([
                'safe_id' => $mainSafe->id,
                'wallet_type' => $walletType,
                'amount' => $amount, // Positive for addition
                'type' => 'daily_transfer',
                'reference_type' => 'daily_transfer',
                'description' => "Daily transfer of {$walletType} from daily safe",
                'user_id' => 1, // System user
            ]);

            $totalTransferred += $amount;
        }

        if ($totalTransferred > 0) {
            $this->info("Successfully transferred {$totalTransferred} from daily to main safe.");
        } else {
            $this->info('No balances to transfer.');
        }
    }
}
