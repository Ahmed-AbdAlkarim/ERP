<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\StockAdjustment;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class StockAdjustmentImport implements ToCollection
{
    protected $batchId;
    protected $userId;

    public function __construct($batchId, $userId)
    {
        $this->batchId = $batchId;
        $this->userId  = $userId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            if ($index == 0) continue; // header

            $product = Product::where('sku', $row[0])->first();
            if (!$product) continue;

            $system = $product->stock;
            $actual = (int)$row[1];

            StockAdjustment::create([
                'product_id' => $product->id,
                'system_qty' => $system,
                'actual_qty' => $actual,
                'difference' => $actual - $system,
                'reason'     => $row[2] ?? 'Excel import',
                'user_id'    => $this->userId,
                'status'     => 'pending',
                'source'     => 'excel',
                'batch_id'   => $this->batchId,
            ]);
        }
    }
}
