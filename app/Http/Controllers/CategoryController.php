<?php

namespace App\Http\Controllers;

use App\Exports\CategoryExport;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $categories = Category::orderBy('created_at', 'desc')->get();
        return response()->json([
            'message' => 'data successfully retrieved',
            'data' => $categories
        ]);
    }

    public function detail(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'error' => 'Category not found',
            ], 404);
        }
        return response()->json([
            'message' => 'data successfully retrieved',
            'data' => $category
        ]);
    }

    public function insert(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:categories,name|string|max:255',
        ]);

        $newCategory = Category::create($validatedData);
        return response()->json([
            'message' => 'Category successfully added',
            'data' => $newCategory
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $id . '|string|max:255',
        ]);

        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'error' => 'Category not found',
            ], 404);
        }
        $category->name = $request->name;
        $category->save();
        return response()->json([
            'message' => 'Category successfully updated',
            'data' => $category
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json([
                'error' => 'Category not found',
            ], 404);
        }
        $category->delete();
        return response()->json([
            'message' => 'Category successfully deleted',
            'data' => $category
        ]);
    }
    public function export_excel(Request $request)
    {
        $date = get_indo_date(date('Y-m-d'));
        $filename = "Daftar Kategori - {$date}.xlsx";
        return Excel::download(new CategoryExport, $filename);
    }
}
