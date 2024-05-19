<?php

namespace App\Http\Controllers;

use App\Models\BarangTransaction;
use Illuminate\Http\Request;

class BarangTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function __invoke(Request $request)
    {
        $barang_transactions = BarangTransaction::with('supplier', 'barang')->get();
        return response()->json([
            'message' => 'data successfully retrieved',
            'data' => $barang_transactions
        ]);
    }

    public function detail(Request $request, $id)
    {
        $barang_transaction = BarangTransaction::with('supplier', 'barang')->find($id);
        if (!$barang_transaction) {
            return response()->json([
                'error' => 'Transaction not found',
            ], 404);
        }
        return response()->json([
            'message' => 'data successfully retrieved',
            'data' => $barang_transaction
        ]);
    }

    public function insert(Request $request)
    {
        $validatedData = $request->validate([
            'transaction_date' => 'required|date',
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'barang_id' => 'required|integer|exists:barangs,id',
            'harga_beli' => 'required|integer|min:0',
            'jumlah' => 'required|integer|min:1',
        ]);

        $newTransaction = BarangTransaction::create($validatedData);
        return response()->json([
            'message' => 'Transaction successfully added',
            'data' => $newTransaction
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'supplier_id' => 'required|integer|exists:suppliers,id',
            'barang_id' => 'required|integer|exists:barangs,id',
            'harga_beli' => 'required|integer|min:0',
            'jumlah' => 'required|integer|min:1',
        ]);

        $barang_transaction = BarangTransaction::find($id);
        if (!$barang_transaction) {
            return response()->json([
                'error' => 'Transaction not found',
            ], 404);
        }
        $barang_transaction->transaction_date = $request->transaction_date;
        $barang_transaction->supplier_id = $request->supplier_id;
        $barang_transaction->barang_id = $request->barang_id;
        $barang_transaction->harga_beli = $request->harga_beli;
        $barang_transaction->jumlah = $request->jumlah;
        $barang_transaction->save();
        return response()->json([
            'message' => 'Category successfully updated',
            'data' => $barang_transaction
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $barang_transaction = BarangTransaction::find($id);
        if (!$barang_transaction) {
            return response()->json([
                'error' => 'Transaction not found',
            ], 404);
        }
        $barang_transaction->delete();
        return response()->json([
            'message' => 'Transaction successfully deleted',
            'data' => $barang_transaction
        ]);
    }
}
