<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockAdjustmentBatch;
use App\Models\StockAdjustment;
use App\Imports\StockAdjustmentImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class StockExcelController extends Controller
{
    public function create()
    {
        return view('admin.inventory.upload');
    }

    public function show($batchId)
    {
        $batch = StockAdjustmentBatch::with([
            'adjustments.product',
            'creator'
        ])->findOrFail($batchId);

        return view('admin.inventory.excel_show', compact('batch'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);

        $path = $request->file('file')->store('stock_excels', 'public');

        $batch = StockAdjustmentBatch::create([
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_path' => $path,
            'created_by' => auth()->id(),
        ]);

        Excel::import(
            new StockAdjustmentImport($batch->id, auth()->id()),
            $request->file('file')
        );

        return redirect()->route('admin.inventory.excel.pending')
            ->with('success','تم رفع الجرد وبانتظار الاعتماد');
    }

    public function pending()
    {
        $batches = StockAdjustmentBatch::where('status','pending')->latest()->get();
        return view('admin.inventory.excel_pending',compact('batches'));
    }

    public function approve($batchId)
    {
        $batch = StockAdjustmentBatch::findOrFail($batchId);

        foreach ($batch->adjustments as $adj) {
            $adj->product->applyApprovedAdjustment($adj);
            $adj->update([
                'status'=>'approved',
                'approved_by'=>auth()->id(),
                'approved_at'=>now()
            ]);
        }

        $batch->update([
            'status'=>'approved',
            'approved_by'=>auth()->id(),
            'approved_at'=>now()
        ]);

        return back()->with('success','تم اعتماد الجرد');
    }

    public function reject($batchId)
    {
        $batch = StockAdjustmentBatch::findOrFail($batchId);

        $batch->adjustments()->update([
            'status' => 'rejected'
        ]);

        $batch->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        return redirect()
            ->route('admin.inventory.excel.pending')
            ->with('success','تم رفض الجرد');
    }


}
