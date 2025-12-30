<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'device_type',
        'fault_type',
        'cost',
        'delivery_date',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];


    protected $casts = [
        'cost' => 'decimal:2',
        'delivery_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function isCollected()
    {
        return \App\Models\CashboxTransaction::where('module', 'maintenance_collection')
            ->where('module_id', $this->id)
            ->exists();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

}
