<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
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
        $barangs = Barang::with('category')->get();
        return response()->json([
            'message' => 'data successfully retrieved',
            'data' => $barangs
        ]);
    }

    public function detail(Request $request, $slug)
    {
        $barang = Barang::where('slug', $slug)->with('category')->first();
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
            'slug' => 'required|string|max:255|unique:categories,slug',
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
            'slug' => 'required|string|max:255|unique:categories,slug',
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
        $barang->slug = $request->slug;
        $barang->name = $request->name;
        $barang->category_id = $request->category_id;
        if ($request->hitung_stok) $barang->hitung_stok = $request->hitung_stok;
        $barang->harga_modal = $request->harga_modal;
        $barang->harga_jual_satuan = $request->harga_jual_satuan;
        $barang->harga_jual_grosir = $request->harga_jual_grosir;
        $barang->harga_jual_reseller = $request->harga_jual_reseller;
        if ($request->stok) $barang->stok = $request->stok;
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
}
