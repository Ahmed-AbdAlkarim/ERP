<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockAdjustment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'system_qty',
        'actual_qty',
        'difference',
        'reason',
        'user_id',
        'status',
        'approved_by',
        'approved_at',
        'source',
        'batch_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function batch()
    {
        return $this->belongsTo(StockAdjustmentBatch::class, 'batch_id');
    }
}
