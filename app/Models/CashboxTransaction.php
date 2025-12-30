<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;
use App\Models\User;

class CashboxTransaction extends Model
{
    protected $fillable = [
        'cashbox_id',
        'type',
        'amount',
        'module',
        'module_id',
        'note',
        'date',
        'user_id',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    public function cashbox()
    {
        return $this->belongsTo(Cashbox::class);
    }

    protected function moduleLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => match ($this->module) {
                'sales_invoice' => 'فاتورة بيع',
                'purchase_invoice' => 'فاتورة شراء',
                'transfer' => 'تحويل خزنة',
                'expense' => 'مصروف',
                'initial_balance' => 'رصيد افتتاحي',
                'maintenance_collection' => 'تحصيل صيانة',
                default => 'غير معروف',
            }
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function formattedDate(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->date?->format('Y-m-d H:i')
        );
    }
}



