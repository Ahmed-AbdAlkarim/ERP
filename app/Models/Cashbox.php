<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cashbox extends Model
{
    protected $fillable = ['name', 'type', 'is_active'];

    public function transactions()
    {
        return $this->hasMany(CashboxTransaction::class);
    }

    public function getBalanceAttribute()
    {
        return $this->transactions()
            ->selectRaw("SUM(CASE WHEN type = 'in' THEN amount ELSE -amount END) as balance")
            ->value('balance') ?? 0;
    }
}

