<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    protected $fillable = [
        'return_number',
        'customer_id',
        'return_date',
        'total_amount',
        'cashbox_id',
        'notes',
    ];

    protected $casts = [
        'return_date' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(SalesReturnItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function cashbox()
    {
        return $this->belongsTo(Cashbox::class);
    }
}

