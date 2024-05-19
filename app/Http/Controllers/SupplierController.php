<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function __invoke(Request $request)
    {
        $suppliers = Supplier::all();
        return response()->json([
            'message' => 'data successfully retrieved',
            'data' => $suppliers
        ]);
    }

    public function detail(Request $request, $id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json([
                'error' => 'Supploer not found',
            ], 404);
        }
        return response()->json([
            'message' => 'data successfully retrieved',
            'data' => $supplier
        ]);
    }

    public function insert(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
        ]);

        $newSupplier = Supplier::create($validatedData);
        return response()->json([
            'message' => 'Supplier successfully added',
            'data' => $newSupplier
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
        ]);

        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json([
                'error' => 'Supplier not found',
            ], 404);
        }
        $supplier->name = $request->name;
        $supplier->address = $request->address;
        $supplier->phone_number = $request->phone_number;
        $supplier->save();
        return response()->json([
            'message' => 'Supplier successfully updated',
            'data' => $supplier
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $supplier = Supplier::find($id);
        if (!$supplier) {
            return response()->json([
                'error' => 'Supplier not found',
            ], 404);
        }
        $supplier->delete();
        return response()->json([
            'message' => 'Supplier successfully deleted',
            'data' => $supplier
        ]);
    }
}
