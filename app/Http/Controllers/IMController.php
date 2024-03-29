<?php
namespace App\Http\Controllers;

use App\Models\IM;
use App\Models\Author;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IMController extends Controller
{
    public function index()
    {
        $ims = IM::with('authors', 'category')
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->get();
        if (request()->ajax()) {
            return response()->json($ims);
        } else {
            return view('inventory_records.manage_masterlist', compact('ims'));
        }
    }
    public function create()
    {
        $authors = Author::orderBy(DB::raw('COALESCE(updated_at, created_at)'), 'desc')->get();
        $categories = Category::orderBy(DB::raw('COALESCE(updated_at, created_at)'), 'desc')->get();
        $data = [
            'authors' => $authors,
            'categories' => $categories,
        ];
        if (request()->ajax()) {
            return response()->json($data);
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
            $request['code'] = formatInput($request['code']);
            $request['title'] = formatInput($request['title']);
            $request['college'] = $request->input('college') ? formatInput($request->input('college')) : null;
            $request['publisher'] = $request->input('publisher') ? formatInput($request->input('publisher')) : null;
            $request['edition'] = $request->input('edition') ? formatInput($request->input('edition')) : null;
            $request['isbn'] = $request->input('isbn') ? formatInput($request->input('isbn')) : null;
            $request['description'] = $request->input('description') ? formatInput($request->input('description')) : null;
            $im = new IM([
                'code' => $request->input('code'),
                'title' => $request->input('title'),
                'category_id' => $request->input('category'),
                'college' => $request->input('college'),
                'publisher' => $request->input('publisher'),
                'edition' => $request->input('edition'),
                'isbn' => $request->input('isbn'),
                'description' => $request->input('description'),
            ]);
            $authors = $request->input('authors', []);
            if (count($authors) > 0) {
                $existingAuthors = Author::whereIn('id', $authors)->count();
                if ($existingAuthors !== count($authors)) {
                    return response()->json(['error' => 'An internal error was detected, please try refreshing the page!'], 422);
                }
            }
            try {
                $im->save();
                $im->authors()->attach($authors);
                return response()->json(['success' => 'The instructional material has been successfully added!'], 200);
            } catch (\Exception $e) {
                return response()->json(['error' => 'An internal error was detected, please try refreshing the page!'], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An internal error was detected, please try refreshing the page!'], 422);
        }
    }
    public function show(IM $im)
    {
    }
    public function edit($id)
    {
        try {
            $im = IM::findOrFail($id);
            $im->load('authors');
            return response()->json($im);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An internal error was detected, please try refreshing the page!'], 422);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $im = IM::findOrFail($id);
            function formatInput(string $input): string
            {
                $input = preg_replace('/\s+/', ' ', trim($input));
                return $input;
            }
            $request['code'] = formatInput($request['code']);
            $request['title'] = formatInput($request['title']);
            $request['college'] = $request->input('college') ? formatInput($request->input('college')) : null;
            $request['publisher'] = $request->input('publisher') ? formatInput($request->input('publisher')) : null;
            $request['edition'] = $request->input('edition') ? formatInput($request->input('edition')) : null;
            $request['isbn'] = $request->input('isbn') ? formatInput($request->input('isbn')) : null;
            $request['description'] = $request->input('description') ? formatInput($request->input('description')) : null;
            $authors = $request->input('authors', []);
            if (count($authors) > 0) {
                $existingAuthors = Author::whereIn('id', $authors)->count();
                if ($existingAuthors !== count($authors)) {
                    return response()->json(['error' => 'An internal error was detected, please try refreshing the page!'], 422);
                }
            }
            try {
                $im->update([
                    'code' => $request->input('code'),
                    'title' => $request->input('title'),
                    'category_id' => $request->input('category'),
                    'college' => $request->input('college'),
                    'publisher' => $request->input('publisher'),
                    'edition' => $request->input('edition'),
                    'isbn' => $request->input('isbn'),
                    'description' => $request->input('description'),
                ]);
                $im->touch();
                $im->authors()->sync($authors);
                return response()->json(['success' => 'The instructional material has been successfully updated!'], 200);
            } catch (\Exception $e) {
                return response()->json(['error' => 'An internal error was detected, please try refreshing the page!'], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An internal error was detected, please try refreshing the page!'], 422);
        }
    }
    public function destroy($id)
    {
        try {
            $im = IM::findOrFail($id);
            if ($im->batches()->exists()) {
                return response()->json(['error' => 'This instructional material holds other records and cannot be deleted!'], 422);
            }
            try {
                $im->authors()->detach();
                $im->delete();
                return response()->json(['success' => 'The instructional material has been successfully deleted!'], 200);
            } catch (\Illuminate\Database\QueryException $e) {
                return response()->json(['error' => 'This instructional material holds other records and cannot be deleted!'], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An internal error was detected, please try refreshing the page!'], 422);
        }
    }
}