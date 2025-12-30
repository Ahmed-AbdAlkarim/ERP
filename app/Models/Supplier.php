<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'phone', 'company', 'debt', 'last_supply_date'
    ];

    protected $casts = [
        'debt' => 'decimal:2',
        'last_supply_date' => 'date',
    ];

    public function debts()
    {
        return $this->hasMany(SupplierDebt::class);
    }

    public function purchaseInvoices()
    {
        return $this->hasMany(PurchaseInvoice::class);
    }
}
