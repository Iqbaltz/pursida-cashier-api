<?php

namespace App\Http\Controllers;

use App\Exports\DaftarBarangExport;
use App\Models\Barang;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BarangController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $barangs = Barang::with('category')->orderBy('created_at', 'desc')->get();
        return response()->json([
            'message' => 'data successfully retrieved',
            'data' => $barangs
        ]);
    }

    public function detail(Request $request, $id)
    {
        $barang = Barang::with('category')->find($id);
        if (!$barang) {
            return response()->json([
                'error' => 'Barang not found',
            ], 404);
        }
        return response()->json([
            'message' => 'data successfully retrieved',
            'data' => $barang
        ]);
    }

    public function insert(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'integer|exists:categories,id',
            'hitung_stok' => 'boolean',
            'harga_modal' => 'required|integer',
            'harga_jual_satuan' => 'required|integer',
            'harga_jual_grosir' => 'required|integer',
            'harga_jual_reseller' => 'required|integer',
            'stok' => 'integer',
        ]);

        $newBarang = Barang::create($validatedData);
        $barang = Barang::with('category')->find($newBarang->id);
        return response()->json([
            'message' => 'Barang successfully added',
            'data' => $barang
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'integer|exists:categories,id',
            'hitung_stok' => 'boolean',
            'harga_modal' => 'required|integer',
            'harga_jual_satuan' => 'required|integer',
            'harga_jual_grosir' => 'required|integer',
            'harga_jual_reseller' => 'required|integer',
            'stok' => 'integer',
        ]);

        $barang = Barang::find($id);
        if (!$barang) {
            return response()->json([
                'error' => 'Barang not found',
            ], 404);
        }
        $barang->name = $request->name;
        $barang->category_id = $request->category_id;
        $barang->harga_modal = $request->harga_modal;
        $barang->harga_jual_satuan = $request->harga_jual_satuan;
        $barang->harga_jual_grosir = $request->harga_jual_grosir;
        $barang->harga_jual_reseller = $request->harga_jual_reseller;
        if ($request->stok) $barang->stok = $request->stok;
        if (isset($request->hitung_stok)) {
            $barang->hitung_stok = $request->hitung_stok;
            if ($barang->hitung_stok == false) $barang->stok = 0;
        }
        $barang->save();
        $updatedBarang = Barang::with('category')->find($barang->id);
        return response()->json([
            'message' => 'Barang successfully updated',
            'data' => $updatedBarang
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $barang = Barang::find($id);
        if (!$barang) {
            return response()->json([
                'error' => 'Barang not found',
            ], 404);
        }
        $barang->delete();
        return response()->json([
            'message' => 'Barang successfully deleted',
            'data' => $barang
        ]);
    }

    public function export_excel(Request $request)
    {
        $date = get_indo_date(date('Y-m-d'));
        $filename = "Daftar barang - {$date}.xlsx";
        return Excel::download(new DaftarBarangExport, $filename);
    }
}
