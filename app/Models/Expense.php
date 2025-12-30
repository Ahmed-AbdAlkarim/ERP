<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'amount',
        'expense_date',
        'attachment',
        'notes',
        'cashbox_id',
    ];


    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function cashbox()
    {
        return $this->belongsTo(Cashbox::class);
    }

    public function getAttachmentUrlAttribute()
    {
        return $this->attachment ? Storage::url($this->attachment) : null;
    }
}
