<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InventoryStockExport implements FromCollection, WithHeadings
{
    protected $type;

    public function __construct($type = null)
    {
        $this->type = $type;
    }

    public function collection()
    {
        $query = Product::select('sku', 'name', 'stock');

        if (!empty($this->type)) {
            $query->where('type', $this->type);
        }

        return $query->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'SKU',
            'اسم المنتج',
            'الكمية بالمخزون',
        ];
    }
}
