<?php

namespace App\Http\Controllers;

use App\Exports\CashierTransactionExport;
use App\Models\Barang;
use App\Models\CashierTransaction;
use App\Models\StoreInformation;
use App\Models\CashierTransactionItem;
use App\Models\Customer;
use App\Models\PaymentMethods;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CashierTransactionController extends Controller
{
    private function get_harga_barang($barang, $type)
    {
        if ($type == 'satuan') {
            return $barang->harga_jual_satuan;
        } else if ($type == 'grosir') {
            return $barang->harga_jual_grosir;
        } else if ($type == 'reseller') {
            return $barang->harga_jual_reseller;
        }
        return 0;
    }

    private function search_barang($barangs, $id)
    {
        foreach ($barangs as $barang) {
            if ($barang->id == $id) {
                return $barang;
            }
        }
        return null;
    }

    private function get_subtotal($barangs)
    {
        $sum = 0;
        foreach ($barangs as $barang) {
            $sum += $barang->price_per_barang * $barang->qty;
        }
        return $sum;
    }

    private function get_subtotal_from_request($barangs, $items)
    {
        $sum = 0;
        foreach ($items as $item) {
            $barang = $this->search_barang($barangs, $item['barang_id']);
            $price = $this->get_harga_barang($barang, $item['transaction_type']);
            $sum += $price * $item['qty'];
        }
        return $sum;
    }

    public function __invoke(Request $request)
    {
        $cashier_transactions = CashierTransaction::with([
            'cashier', 'customer', 'payment_method', 'transaction_items'
        ]);

        if ($request->search) {
            $cashier_transactions->where('transaction_number', 'LIKE', "%$request->search%")
                ->orWhereHas('cashier', function ($q) use ($request) {
                    $q->where('name', 'LIKE', "%$request->search%");
                })->orWhereHas('customer', function ($q) use ($request) {
                    $q->where('name', 'LIKE', "%$request->search%");
                });
        }
        if ($request->orders) {
            foreach ($request->orders as $orderObj) {
                $orderBy = $orderObj['id'];
                $orderType = $orderObj['desc'] == 'false' ? 'ASC' : 'DESC';
                if ($orderBy == 'total_items') {
                    $cashier_transactions->select('cashier_transactions.*')
                        ->leftJoin('cashier_transaction_items', 'cashier_transaction_items.cashier_transaction_id', '=', 'cashier_transactions.id')
                        ->groupBy('cashier_transactions.id')
                        ->orderByRaw('SUM(cashier_transaction_items.qty) ' . $orderType);
                } else if ($orderBy == 'total_payment') {
                    $cashier_transactions->select('cashier_transactions.*')
                        ->leftJoin('cashier_transaction_items', 'cashier_transaction_items.cashier_transaction_id', '=', 'cashier_transactions.id')
                        ->groupBy('cashier_transactions.id')
                        ->orderByRaw('SUM(cashier_transaction_items.price_per_barang * cashier_transaction_items.qty) ' . $orderType);
                } else {
                    $cashier_transactions->orderBy($orderBy, $orderType);
                }
            }
        } else {
            $cashier_transactions->orderBy('updated_at', 'DESC');
        }

        return response()->json([
            'message' => 'data successfully retrieved',
            'data' => $cashier_transactions->paginate($this->per_page())
        ]);
    }

    public function detail(Request $request, $id)
    {
        $cashier_transaction = CashierTransaction::with([
            'cashier', 'customer', 'payment_method', 'transaction_items'
        ])->find($id);
        if (!$cashier_transaction) {
            return response()->json([
                'error' => 'Transaction not found',
            ], 404);
        }
        return response()->json([
            'message' => 'data successfully retrieved',
            'data' => $cashier_transaction
        ]);
    }

    public function insert(Request $request)
    {
        $validatedData = $request->validate([
            'transaction_date' => 'required|date',
            'cashier_id' => 'required|integer|exists:users,id',
            'customer_id' => 'nullable|integer|exists:customers,id',
            'payment_method_id' => 'required|integer|exists:payment_methods,id',
            'discount' => 'required|numeric|min:0',
            'payment_amount' => 'required|numeric|min:0',
            'items' => 'required|array',
            'items.*.barang_id' => 'required|integer|exists:barangs,id',
            'items.*.transaction_type' => 'required|in:satuan,grosir,reseller',
            'items.*.qty' => 'required|numeric|min:0',
        ]);

        $cashierTransactionId = null;
        DB::transaction(function () use ($validatedData, &$cashierTransactionId, $request) {
            $date = Carbon::now()->format('ymd');
            $count = CashierTransaction::whereDate('created_at', Carbon::today())->count() + 1;
            $sequence = str_pad($count, 4, '0', STR_PAD_LEFT);
            $transaction_number = 'TRXN' . $date . $sequence;

            $barangs = Barang::all();
            $subtotal = $this->get_subtotal_from_request($barangs, $request->items);
            $subtotal = $subtotal - $validatedData['discount'];
            $cashier = User::find($request->cashier_id);
            $payment_method = PaymentMethods::find($request->payment_method_id);
            $customer = null;
            if ($request->customer_id) {
                $customer = Customer::find($request->customer_id);
            }
            // Create the cashier transaction
            $cashierTransaction = CashierTransaction::create([
                'cashier_name' => $cashier->name,
                'payment_method_name' => $payment_method->name,
                'customer_name' => $customer ? $customer->name : 'UMUM',
                'transaction_number' => $transaction_number,
                'transaction_date' => $validatedData['transaction_date'],
                'cashier_id' => $validatedData['cashier_id'],
                'customer_id' => $validatedData['customer_id'],
                'payment_method_id' => $validatedData['payment_method_id'],
                'discount' => $validatedData['discount'],
                'payment_amount' => $validatedData['payment_amount'],
                'payment_status' => $subtotal - $validatedData['payment_amount'] <= 0 ? 1 : 0
            ]);

            $cashierTransactionId = $cashierTransaction->id;
            // Create each cashier transaction item
            foreach ($validatedData['items'] as $item) {
                $barang = Barang::find($item['barang_id']);
                if ($barang->hitung_stok == true) {
                    if ($barang->stok < $item['qty']) {
                        throw new \Exception("Insufficient stock for item: {$barang->name}");
                    }
                    $barang->stok = $barang->stok - $item['qty'];
                }
                $barang->save();
                CashierTransactionItem::create([
                    'cashier_transaction_id' => $cashierTransaction->id,
                    'barang_id' => $item['barang_id'],
                    'barang_name' => $barang->name,
                    'harga_modal' => $barang->harga_modal,
                    'price_per_barang' => $this->get_harga_barang($barang, $item['transaction_type']),
                    'transaction_type' => $item['transaction_type'],
                    'qty' => $item['qty'],
                ]);
            }
        });

        $cashier_transaction = CashierTransaction::with([
            'cashier', 'customer', 'payment_method', 'transaction_items'
        ])->find($cashierTransactionId);

        return response()->json([
            'message' => 'Transaction successfully added',
            'data' => $cashier_transaction
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validate the main cashier transaction data
        $validatedData = $request->validate([
            'transaction_number' => 'string|max:20',
            'transaction_date' => 'required|date',
            'cashier_id' => 'required|integer|exists:users,id',
            'customer_id' => 'nullable|integer|exists:customers,id',
            'payment_method_id' => 'required|integer|exists:payment_methods,id',
            'discount' => 'required|numeric|min:0',
            'payment_amount' => 'required|numeric|min:0',
            'items' => 'required|array',
            'items.*.id' => 'nullable|integer|exists:cashier_transaction_items,id',
            'items.*.barang_id' => 'required|integer|exists:barangs,id',
            'items.*.transaction_type' => 'required|in:satuan,grosir,reseller',
            'items.*.qty' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validatedData, $id, $request) {
            // Find the cashier transaction
            $cashierTransaction = CashierTransaction::findOrFail($id);

            $cashier = User::find($request->cashier_id);
            $payment_method = PaymentMethods::find($request->payment_method_id);
            $customer = null;
            if ($request->customer_id) {
                $customer = Customer::find($request->customer_id);
            }
            $barangs = Barang::all();
            $subtotal = $this->get_subtotal_from_request($barangs, $request->items);
            $subtotal = $subtotal - $validatedData['discount'];
            // Update the cashier transaction
            $updatedData = [
                'cashier_name' => $cashier->name,
                'payment_method_name' => $payment_method->name,
                'customer_name' => $customer ? $customer->name : 'UMUM',
                'transaction_date' => $validatedData['transaction_date'],
                'cashier_id' => $validatedData['cashier_id'],
                'customer_id' => $validatedData['customer_id'],
                'payment_method_id' => $validatedData['payment_method_id'],
                'discount' => $validatedData['discount'],
                'payment_amount' => $validatedData['payment_amount'],
                'payment_status' => $subtotal - $validatedData['payment_amount'] <= 0 ? 1 : 0
            ];

            if (isset($validatedData['transaction_number'])) {
                $updatedData['transaction_number'] = $validatedData['transaction_number'];
            }
            $cashierTransaction->update($updatedData);

            // Collect the IDs of existing items to update
            $existingItemIds = collect($validatedData['items'])->pluck('id')->filter()->all();

            // Delete items that are not in the request
            CashierTransactionItem::where('cashier_transaction_id', $cashierTransaction->id)
                ->whereNotIn('id', $existingItemIds)
                ->delete();

            // Update or create items
            foreach ($validatedData['items'] as $item) {
                $barang = $this->search_barang($barangs, $item['barang_id']);
                if (isset($item['id'])) {
                    // Update existing item
                    CashierTransactionItem::where('id', $item['id'])->update([
                        'barang_id' => $item['barang_id'],
                        'barang_name' => $barang->name,
                        'harga_modal' => $barang->harga_modal,
                        'price_per_barang' => $this->get_harga_barang($barang, $item['transaction_type']),
                        'transaction_type' => $item['transaction_type'],
                        'qty' => $item['qty'],
                    ]);
                } else {
                    // Create new item
                    CashierTransactionItem::create([
                        'cashier_transaction_id' => $cashierTransaction->id,
                        'barang_id' => $item['barang_id'],
                        'barang_name' => $barang->name,
                        'harga_modal' => $barang->harga_modal,
                        'price_per_barang' => $this->get_harga_barang($barang, $item['transaction_type']),
                        'transaction_type' => $item['transaction_type'],
                        'qty' => $item['qty'],
                    ]);
                }
            }
        });

        // Retrieve the updated cashier transaction with its items
        $cashierTransaction = CashierTransaction::with([
            'cashier', 'customer', 'payment_method', 'transaction_items'
        ])->find($id);

        return response()->json([
            'message' => 'Transaction successfully updated',
            'data' => $cashierTransaction
        ]);
    }

    public function destroy($id)
    {
        $cashierTransaction = CashierTransaction::findOrFail($id);
        $cashierTransaction->transaction_items()->delete();
        $cashierTransaction->delete();

        return response()->json([
            'message' => 'Transaction and associated items deleted successfully',
            'data' => $cashierTransaction
        ]);
    }

    public function get_receipt_html(Request $request, $id)
    {
        $transaction = CashierTransaction::with([
            'cashier', 'customer', 'payment_method', 'transaction_items' => function ($q) {
                $q->with('barang');
            }
        ])->find($id);

        $store_information = StoreInformation::first();

        if (!$transaction) {
            return response()->json([
                'error' => 'Transaction not found',
            ], 404);
        }

        $subtotal = $this->get_subtotal($transaction->transaction_items);
        $total = $subtotal - $transaction->discount;
        $data = [
            'store_name' => $store_information->name,
            'store_address' => $store_information->address,
            'store_phone_number' => $store_information->phone_number,
            'no_nota' => $transaction->transaction_number,
            'kasir' => $transaction->cashier_name,
            'pelanggan' => $transaction->customer_name,
            'alamat' => $transaction->customer ? $transaction->customer->address : '-',
            'no_telp' => $transaction->customer ? $transaction->customer->phone_number : '-',
            'items' => $transaction->transaction_items->map(fn ($x, $idx) => [
                'no' => $idx + 1,
                'name' => $x->barang_name,
                'qty' => $x->qty,
                'price' => $x->price_per_barang,
                'amount' => $x->qty * $x->price_per_barang
            ]),
            'subtotal' => $subtotal,
            'diskon' => $transaction->discount,
            'total' => $total,
            'tunai' => $transaction->payment_amount,
            'kembalian' => -1 * ($total - $transaction->payment_amount),
        ];

        return view('pdf/cashier-transaction-html-landscape', $data);
    }

    public function print_receipt(Request $request, $id)
    {
        $transaction = CashierTransaction::with([
            'cashier', 'customer', 'payment_method', 'transaction_items' => function ($q) {
                $q->with('barang');
            }
        ])->find($id);

        $store_information = StoreInformation::first();

        if (!$transaction) {
            return response()->json([
                'error' => 'Transaction not found',
            ], 404);
        }
        $subtotal = $this->get_subtotal($transaction->transaction_items);
        $total = $subtotal - $transaction->discount;
        $data = [
            'store_name' => $store_information->name,
            'store_address' => $store_information->address,
            'store_phone_number' => $store_information->phone_number,
            'no_nota' => $transaction->transaction_number,
            'kasir' => $transaction->cashier_name,
            'pelanggan' => $transaction->customer_name,
            'alamat' => $transaction->customer ? $transaction->customer->address : '-',
            'no_telp' => $transaction->customer ? $transaction->customer->phone_number : '-',
            'items' => $transaction->transaction_items->map(fn ($x, $idx) => [
                'no' => $idx + 1,
                'name' => $x->barang_name,
                'qty' => $x->qty,
                'price' => $x->price_per_barang,
                'amount' => $x->qty * $x->price_per_barang
            ]),
            'subtotal' => $subtotal,
            'diskon' => $transaction->discount,
            'total' => $total,
            'tunai' => $transaction->payment_amount,
            'kembalian' => -1 * ($total - $transaction->payment_amount),
        ];

        $pdf = Pdf::loadView('pdf/cashier-transaction', $data);
        $content = view('pdf/cashier-transaction', $data)->render();
        $lineHeight = 2; // Adjust based on your font size and line height
        $numberOfLines = substr_count($content, "\n") + 2;
        $estimatedHeight = $numberOfLines * $lineHeight;

        // Set paper size to fit the content
        $pdf->setPaper([0, 0, 240, $estimatedHeight], 'portrait');
        $filename = "invoice {$transaction->transaction_number}.pdf";

        return $pdf->stream($filename);
    }
    public function export_excel(Request $request)
    {
        $date = get_indo_date(date('Y-m-d'));
        $filename = "Daftar transaksi kasir - {$date}.xlsx";
        $start_date = $request->start_date ?? null;
        $end_date = $request->end_date ?? null;
        if ($start_date || $end_date) {
            $filename = "Daftar transaksi kasir - {$date} - filtered.xlsx";
        }
        // date format: 2024-01-01
        return Excel::download(new CashierTransactionExport($start_date, $end_date), $filename);
    }
}
