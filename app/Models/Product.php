<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\StockMovement;
use App\Models\StockAdjustment;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','type','model','sku','purchase_price','selling_price',
        'min_allowed_price','warranty_type','warranty_period_days',
        'condition','image','stock','reorder_level','is_service','notes','avg_cost'
    ];

    protected $casts = [
        'stock' => 'integer',
        'reorder_level' => 'integer',
    ];

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }

    public function recalcPurchaseData()
    {
        // جلب كل عناصر الفاتورة الخاصة بالمنتج
        $items = $this->purchaseItems()->get();

        if ($items->count() == 0) {
            $this->avg_cost = 0;
            $this->purchase_price = 0;
            $this->selling_price = 0;
            $this->min_allowed_price = 0;
            return $this->save();
        }

        $totalQty = $items->sum('quantity');
        $totalCost = $items->sum(function($i){
            return $i->quantity * $i->purchase_price;
        });

        $avg = $totalQty ? $totalCost / $totalQty : 0;

        $this->avg_cost = round($avg, 2);
        $this->purchase_price = round($avg, 2);

        $profitPercentage = 20; // نسبة الربح على سعر البيع
        $this->selling_price = round($avg * (1 + $profitPercentage / 100), 2);

        $this->min_allowed_price = round($avg * 1.05, 2); // أقل سعر مسموح 5% فوق التكلفة

        return $this->save();
    }


    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function isLowStock(): bool
    {
        if ($this->is_service) return false;

        return (int)$this->stock <= (int)$this->reorder_level;
    }


    /**
     * زيادة المخزون
     */
    public function addStock(int $qty, ?string $note = null, $reference = null)
    {
        DB::transaction(function () use ($qty, $note, $reference) {
            $prod = $this->lockForUpdate();
            $prod->refresh();
            $before = (int)$prod->stock;
            $after  = $before + $qty;
            $prod->update(['stock' => $after]);

            StockMovement::create([
                'product_id' => $prod->id,
                'type'       => 'in',
                'quantity'   => $qty,
                'before_qty' => $before,
                'after_qty'  => $after,
                'note'       => $note,
                'reference_type' => $reference['type'] ?? null,
                'reference_id'   => $reference['id'] ?? null,
            ]);
        });
    }

    /**
     * خصم المخزون
     */
    public function removeStock(int $qty, ?string $note = null, $reference = null)
    {
        DB::transaction(function () use ($qty, $note, $reference) {
            $prod = $this->lockForUpdate();
            $prod->refresh();
            $before = (int)$prod->stock;
            $after  = max(0, $before - $qty);

            $prod->update(['stock' => $after]);

            StockMovement::create([
                'product_id' => $prod->id,
                'type'       => 'out',
                'quantity'   => $qty,
                'before_qty' => $before,
                'after_qty'  => $after,
                'note'       => $note,
                'reference_type' => $reference['type'] ?? null,
                'reference_id'   => $reference['id'] ?? null,
            ]);
        });
    }

    /**
     * تسوية المخزون الى actualQty
     */
    public function adjustStock(int $actualQty, ?string $reason = null, $userId = null)
    {
        DB::transaction(function () use ($actualQty, $reason, $userId) {
            $prod = self::where('id', $this->id)->lockForUpdate()->first();

            $systemQty = (int)$prod->stock;
            $difference = $actualQty - $systemQty;

            // سجل التسوية
            StockAdjustment::create([
                'product_id' => $prod->id,
                'system_qty' => $systemQty,
                'actual_qty' => $actualQty,
                'difference' => $difference,
                'reason'     => $reason,
                'user_id'    => $userId,
                'status'     => 'approved',
                'approved_by' => $userId,
                'approved_at' => now(),
            ]);

            // حدث الكمية
            $prod->update(['stock' => $actualQty]);

            // سجل الحركة
            StockMovement::create([
                'product_id' => $prod->id,
                'type'       => 'adjustment',
                'quantity'   => abs($difference),
                'before_qty' => $systemQty,
                'after_qty'  => $actualQty,
                'note'       => $reason ?? 'Stock adjustment',
            ]);
        });
    }

    // App\Models\Product.php
    public function applyApprovedAdjustment(StockAdjustment $adj)
    {
        DB::transaction(function () use ($adj) {

            $prod = self::where('id',$adj->product_id)->lockForUpdate()->first();

            $before = $prod->stock;
            $after  = $adj->actual_qty;

            $prod->update(['stock' => $after]);

            StockMovement::create([
                'product_id' => $prod->id,
                'type'       => 'adjustment',
                'quantity'   => abs($after - $before),
                'before_qty' => $before,
                'after_qty'  => $after,
                'note' => '[Excel] ' . ($adj->reason ?? 'Stock adjustment'),

            ]);
        });
    }

}
