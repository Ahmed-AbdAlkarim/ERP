<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturnItem extends Model
{
    protected $fillable = [
        'sales_return_id',
        'product_id',
        'qty',
        'return_price',
        'total',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function salesReturn()
    {
        return $this->belongsTo(SalesReturn::class);
    }
}

