<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SalesInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'user_id',
        'customer_id',
        'subtotal',
        'discount',
        'total',
        'paid_amount',
        'remaining_amount',
        'payment_method',
        'status',
        'profit',
        'invoice_pdf',
        'invoice_image',
        'notes'
    ];

    protected $casts = [
        'invoice_date' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
