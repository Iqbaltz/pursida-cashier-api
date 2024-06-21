<?php

namespace App\Http\Controllers;

use App\Exports\SupplierExport;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SupplierController extends Controller
{
    public function __invoke(Request $request)
    {
        $suppliers = Supplier::orderBy('created_at', 'desc')->get();
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
                'error' => 'Supplier not found',
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
            'address' => 'string|max:255',
            'phone_number' => 'string|max:20',
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
            'name' => 'required|string|unique:suppliers,name,' . $id . '|max:255',
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
    public function export_excel(Request $request)
    {
        $date = get_indo_date(date('Y-m-d'));
        $filename = "Daftar Supplier - {$date}.xlsx";
        return Excel::download(new SupplierExport, $filename);
    }
}
