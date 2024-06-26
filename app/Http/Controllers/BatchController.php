<?php
namespace App\Http\Controllers;

use App\Models\AdjustmentLog;
use App\Models\Batch;
use App\Models\Im;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        $selectedAuthor = $request->input('select_author');
        $selectedCategory = $request->input('select_category');
        $selectedCollege = $request->input('select_college');
        $selectedDepartment = $request->input('select_department');
        $query = Batch::with('im', 'purchases', 'adjustment_logs');
        if (!empty($selectedAuthor)) {
            $query->whereHas('im.authors', function ($q) use ($selectedAuthor) {
                $q->where('author_id', $selectedAuthor);
            });
        }
        if (!empty($selectedCategory)) {
            $query->whereHas('im.category', function ($q) use ($selectedCategory) {
                $q->where('category_id', $selectedCategory);
            });
        }
        if (!empty($selectedCollege)) {
            $query->whereHas('im.college', function ($q) use ($selectedCollege) {
                $q->where('college_id', $selectedCollege);
            });
        }
        if (!empty($selectedDepartment)) {
            $query->whereHas('im.department', function ($q) use ($selectedDepartment) {
                $q->where('department_id', $selectedDepartment);
            });
        }
        $batches = $query->select(
            'batches.id',
            'batches.im_id',
            'batches.name',
            'batches.production_date',
            'batches.production_cost',
            'batches.price',
            'batches.quantity_produced',
            DB::raw('(SELECT COALESCE(SUM(quantity_sold), 0) FROM purchases WHERE batch_id = batches.id) as quantity_sold'),
            DB::raw('(SELECT COALESCE(SUM(quantity_deducted), 0) FROM adjustment_logs WHERE batch_id = batches.id) as quantity_deducted')
        )
            ->groupBy('batches.id', 'batches.im_id', 'batches.name', 'batches.production_date', 'batches.production_cost', 'batches.price', 'batches.quantity_produced')
            ->orderByDesc('batches.updated_at')
            ->orderByDesc('batches.created_at')
            ->get();
        if ($request->ajax()) {
            return response()->json($batches);
        } else {
            return view('inventory_records.manage_batches', compact('batches'));
        }
    }
    public function create()
    {
        $ims = Im::orderBy(DB::raw('COALESCE(updated_at, created_at)'), 'desc')->get();
        if (request()->ajax()) {
            return response()->json($ims);
        }
    }
    public function store(Request $request)
    {
        try {
            function formatInput(string $input): string
            {
                $input = preg_replace('/\s+/', ' ', trim($input));
                return $input;
            }
            $request['name'] = formatInput($request['name']);
            $production_cost = str_replace(',', '', $request->input('production_cost'));
            $price = str_replace(',', '', $request->input('price'));
            $batch = new Batch([
                'im_id' => $request->input('im_id'),
                'name' => $request->input('name'),
                'production_date' => $request->input('production_date'),
                'production_cost' => $production_cost,
                'price' => $price,
                'quantity_produced' => $request->input('quantity_produced'),
            ]);
            $batch->save();
            return response()->json(['success' => 'The batch has been successfully added!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An internal error was detected, please try refreshing the page!'], 422);
        }
    }
    public function show(batch $batch)
    {
    }
    public function edit($id)
    {
        try {
            $batch = Batch::findOrFail($id);
            return response()->json($batch);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An internal error was detected, please try refreshing the page!'], 422);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $batch = Batch::findOrFail($id);
            function formatInput(string $input): string
            {
                $input = preg_replace('/\s+/', ' ', trim($input));
                return $input;
            }
            $request['name'] = formatInput($request['name']);
            $production_cost = str_replace(',', '', $request->input('production_cost'));
            $price = str_replace(',', '', $request->input('price'));
            $quantitySold = Purchase::where('batch_id', $batch->id)->sum('quantity_sold');
            if ($quantitySold > 0) {
                return response()->json(['error' => 'This batch holds other records and cannot be updated!'], 422);
            }
            $quantityDeducted = AdjustmentLog::where('batch_id', $batch->id)->sum('quantity_deducted');
            if ($quantityDeducted > 0) {
                return response()->json(['error' => 'This batch holds other records and cannot be updated!'], 422);
            }
            $batch->update([
                'im_id' => $request->input('im_id'),
                'name' => $request->input('name'),
                'production_date' => $request->input('production_date'),
                'production_cost' => $production_cost,
                'price' => $price,
                'quantity_produced' => $request->input('quantity_produced'),
            ]);
            return response()->json(['success' => 'The batch has been successfully updated!'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An internal error was detected, please try refreshing the page!'], 422);
        }
    }
    public function destroy($id)
    {
        try {
            $batch = Batch::findOrFail($id);
            $batch->delete();
            return response()->json(['success' => 'The batch has been successfully deleted!'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'An internal error was detected, please try refreshing the page!'], 422);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(['error' => 'This batch holds other records and cannot be deleted!'], 422);
        }
    }
}