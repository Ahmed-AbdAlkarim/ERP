<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\User;
use App\Notifications\LowStockNotification;

class CheckLowStock extends Command
{
    protected $signature = 'check:lowstock';
    protected $description = 'Check products low stock and send notifications';

    public function handle()
    {
        $products = Product::where('track_inventory', true)
            ->whereColumn('stock', '<=', 'reorder_level')
            ->where(function($q){
                $q->whereNull('stock_alert_sent_at')
                  ->orWhere('stock_alert_sent_at', '<', now()->subHours(24));
            })->get();

        if ($products->isEmpty()) {
            $this->info('No low stock products.');
            return;
        }

        $managers = User::where('role', 'inventory_manager')->get();
        foreach ($products as $p) {
            foreach ($managers as $m) {
                $m->notify(new LowStockNotification($p));
            }
            $p->update(['stock_alert_sent_at' => now()]);
        }

        $this->info('Low stock notifications sent: ' . $products->count());
    }
}
