<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number','supplier_id','date','note','total_cost','paid_amount','due_amount','payment_status','additional_expenses',
    ];

    protected $casts = [
        'date' => 'date',
        'total_cost' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseInvoiceItem::class);
    }

    public function debts()
    {
        return $this->hasMany(SupplierDebt::class, 'purchase_invoice_id');
    }

    public function payments()
    {
        return $this->hasMany(CashboxTransaction::class, 'module_id')
                    ->where('module', 'purchase_invoice');
    }

    
}
