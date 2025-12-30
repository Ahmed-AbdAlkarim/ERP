<?php

namespace App\Observers;

use App\Models\SalesInvoice;
use App\Models\Cashbox;

class SalesInvoiceObserver
{
    /**
     * Handle the SalesInvoice "created" event.
     */
    public function created(SalesInvoice $salesInvoice): void
    {
        // Add sales total to daily safe wallet based on payment method
        $dailySafe = \App\Models\Safe::where('name', 'daily_safe')->first();
        if ($dailySafe) {
            $walletType = $salesInvoice->payment_method; // 'cash', 'visa', 'vodafone'
            $wallet = $dailySafe->wallets()->where('wallet_type', $walletType)->first();
            if ($wallet) {
                $wallet->addAmount(
                    $salesInvoice->total,
                    'sale',
                    "Sales invoice #{$salesInvoice->invoice_number}",
                    $salesInvoice->id,
                    'sales_invoice'
                );
            }
        }
    }

    /**
     * Handle the SalesInvoice "updated" event.
     */
    public function updated(SalesInvoice $salesInvoice): void
    {
        //
    }

    /**
     * Handle the SalesInvoice "deleted" event.
     */
    public function deleted(SalesInvoice $salesInvoice): void
    {
        // Subtract sales total from daily cashbox if it was added
        $dailyCashbox = Cashbox::getDailyTreasury();
        if ($dailyCashbox && $salesInvoice->payment_method == 'cash') {
            $dailyCashbox->subtractAmount(
                $salesInvoice->total,
                'sale',
                "Deleted sales invoice #{$salesInvoice->invoice_number}",
                $salesInvoice->id
            );
        }
    }

    /**
     * Handle the SalesInvoice "restored" event.
     */
    public function restored(SalesInvoice $salesInvoice): void
    {
        //
    }

    /**
     * Handle the SalesInvoice "force deleted" event.
     */
    public function forceDeleted(SalesInvoice $salesInvoice): void
    {
        //
    }
}
