<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SupplierDebt extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id','purchase_invoice_id','amount','type','notes','date'
    ];

    protected static function booted()
    {
        static::created(fn($d) => self::recalculate($d->supplier_id));
        static::updated(fn($d) => self::recalculate($d->supplier_id));
        static::deleted(fn($d) => self::recalculate($d->supplier_id));
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class, 'purchase_invoice_id');
    }

    public static function recalculate($supplierId)
    {
        if (! $supplierId) return;

        $debits = self::where('supplier_id', $supplierId)->where('type', 'debit')->sum('amount');
        $credits = self::where('supplier_id', $supplierId)->where('type', 'credit')->sum('amount');

        $balance = round(max(0, $debits - $credits), 2);

        DB::table('suppliers')->where('id', $supplierId)->update(['debt' => $balance, 'updated_at' => now()]);
    }
}
