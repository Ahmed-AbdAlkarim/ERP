<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InventoryStockExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Product::select(
            'sku',
            'name',
            'stock'
        )->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'SKU',
            'اسم المنتج',
            'الكمية في النظام'
        ];
    }
}
