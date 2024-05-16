<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function __invoke(Request $request)
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function detail(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->first();
        if (!$category) {
            return response()->json([
                'error' => 'Category not found',
            ], 404);
        }
        return response()->json($category);
    }

    public function insert(Request $request)
    {
        $validatedData = $request->validate([
            'slug' => 'required|string|max:255|unique:categories,slug',
            'name' => 'required|string|max:255',
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
            'name' => 'required|string|max:255',
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
}
