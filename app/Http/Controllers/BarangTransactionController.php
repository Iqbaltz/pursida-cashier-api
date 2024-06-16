<?php

namespace App\Http\Controllers;

use App\Exports\DaftarTransaksiBarangExport;
use App\Models\Barang;
use App\Models\BarangTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class BarangTransactionController extends Controller
{
    public function __invoke(Request $request)
    {
        $barang_transactions = BarangTransaction::with('supplier', 'barang');
        if ($request->search) {
            $barang_transactions->whereHas('supplier', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%$request->search%");
            })->orWhereHas('barang', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%$request->search%");
            });
        }
        if ($request->orders) {
            foreach ($request->orders as $orderObj) {
                $orderBy = $orderObj['id'];
                $orderType = $orderObj['desc'] == 'false' ? 'ASC' : 'DESC';
                if ($orderBy == 'total') {
                    $barang_transactions->orderByRaw('harga_beli * jumlah ' . $orderType);
                } else {
                    $barang_transactions->orderBy($orderBy, $orderType);
                }
            }
        } else {
            $barang_transactions->orderBy('updated_at', 'DESC');
        }

        return response()->json([
            'message' => 'data successfully retrieved',
            'data' => $barang_transactions->paginate($this->per_page()),
            'summary' => $barang_transactions->sum(DB::raw('harga_beli * jumlah'))
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
        $newTransaction = null;
        DB::transaction(function () use ($request, &$newTransaction) {
            $validatedData = $request->validate([
                'transaction_date' => 'required|date',
                'supplier_id' => 'required|integer|exists:suppliers,id',
                'barang_id' => 'required|integer|exists:barangs,id',
                'harga_beli' => 'required|integer|min:0',
                'jumlah' => 'required|integer|min:1',
            ]);
            $validatedData['transaction_date'] = Carbon::parse($validatedData['transaction_date']);
            $barang = Barang::find($validatedData['barang_id']);
            if ($barang->hitung_stok == true) {
                $barang->stok = $barang->stok + $validatedData['jumlah'];
            }
            $barang->save();

            $newTransaction = BarangTransaction::create($validatedData);
        });
        return response()->json([
            'message' => 'Transaction successfully added',
            'data' => $newTransaction
        ]);
    }

    public function update(Request $request, $id)
    {
        $barang_transaction = null;
        DB::transaction(function () use ($request, $id, &$barang_transaction) {
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
            $barang = Barang::find($request['barang_id']);
            if ($barang->hitung_stok == true) {
                $barang->stok = $barang->stok - ($barang_transaction->jumlah - $request->jumlah);
            }
            $barang->save();
            $barang_transaction->transaction_date = $request->transaction_date;
            $barang_transaction->transaction_date = Carbon::parse($request->transaction_date);
            $barang_transaction->supplier_id = $request->supplier_id;
            $barang_transaction->barang_id = $request->barang_id;
            $barang_transaction->harga_beli = $request->harga_beli;
            $barang_transaction->jumlah = $request->jumlah;
            $barang_transaction->save();
        });
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

    public function export_excel(Request $request)
    {
        $date = get_indo_date(date('Y-m-d'));
        $filename = "Daftar transaksi barang masuk - {$date}.xlsx";
        $start_date = $request->start_date ?? null;
        $end_date = $request->end_date ?? null;
        if ($start_date || $end_date) {
            $filename = "Daftar transaksi barang masuk - {$date} - filtered.xlsx";
        }
        // date format: 2024-01-01
        return Excel::download(new DaftarTransaksiBarangExport($start_date, $end_date), $filename);
    }
}
