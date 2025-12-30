<?php

namespace App\Services;

use App\Models\Cashbox;
use App\Models\CashboxTransaction;
use Illuminate\Support\Facades\DB;

class CashboxService
{
    public function addTransaction($cashboxId, $type, $amount, $module, $moduleId = null, $note = null, $date = null)
    {
        $cashbox = Cashbox::findOrFail($cashboxId);

        if ($type === 'out' && $cashbox->balance < $amount) {
            throw new \Exception('رصيد الخزنة غير كافي');
        }

        DB::transaction(function () use ($cashbox, $type, $amount, $module, $moduleId, $note, $date) {
            CashboxTransaction::create([
                'cashbox_id' => $cashbox->id,
                'type'      => $type,
                'amount'    => $amount,
                'module'    => $module,
                'module_id' => $moduleId,
                'note'      => $note,
                'date'      => $date ?? now(),
                'user_id'   => auth()->id(),
            ]);

            if ($type === 'in') {
                $cashbox->increment('balance', $amount);
            } else {
                $cashbox->decrement('balance', $amount);
            }
        });
    }

    public function revertTransaction($cashboxId, $amount, $module, $moduleId = null)
    {
        $cashbox = Cashbox::findOrFail($cashboxId);

        DB::transaction(function () use ($cashbox, $amount, $module, $moduleId) {
            CashboxTransaction::create([
                'cashbox_id' => $cashbox->id,
                'type'      => 'in',
                'amount'    => $amount,
                'module'    => $module,
                'module_id' => $moduleId,
                'note'      => 'استرجاع حركة مرتبطة بـ ' . $module . ' #' . $moduleId,
                'date'      => now(),
                'user_id'   => auth()->id(),
            ]);
            $cashbox->increment('balance', $amount);
        });
    }

    public function transfer(int $fromCashboxId, int $toCashboxId, float $amount, string $notes = null)
    {
        DB::transaction(function () use ($fromCashboxId, $toCashboxId, $amount, $notes) {
            $this->addTransaction($fromCashboxId, 'out', $amount, 'transfer', null, 'تحويل للخزنة الأخرى');
            $this->addTransaction($toCashboxId, 'in', $amount, 'transfer', null, $notes ?? 'تحويل وارد');
        });
    }

    public function getBalance(int $cashboxId): float
    {
        return Cashbox::findOrFail($cashboxId)->balance;
    }

    public function hasSufficientBalance(int $cashboxId, float $amount): bool
    {
        return Cashbox::findOrFail($cashboxId)->balance >= $amount;
    }
}
