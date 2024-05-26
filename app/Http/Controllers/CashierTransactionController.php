<?php

namespace App\Http\Controllers;

use App\Models\CashierTransaction;
use App\Models\CashierTransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierTransactionController extends Controller
{
    public function __invoke(Request $request)
    {
        $cashier_transactions = CashierTransaction::with([
            'cashier', 'customer', 'payment_method', 'transaction_items'
        ])->paginate($this->per_page());

        return response()->json([
            'message' => 'data successfully retrieved',
            'data' => $cashier_transactions
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
            'customer_id' => 'required|integer|exists:customers,id',
            'payment_method_id' => 'required|integer|exists:payment_methods,id',
            'discount' => 'required|numeric|min:0',
            'items' => 'required|array',
            'items.*.barang_id' => 'required|integer|exists:barangs,id',
            'items.*.transaction_type' => 'required|in:satuan,grosir,reseller',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $cashierTransactionId = null;
        DB::transaction(function () use ($validatedData, &$cashierTransactionId) {
            $date = Carbon::now()->format('ymd');
            $count = CashierTransaction::whereDate('created_at', Carbon::today())->count() + 1;
            $sequence = str_pad($count, 4, '0', STR_PAD_LEFT);
            $transaction_number = 'TRXN' . $date . $sequence;
            // Create the cashier transaction
            $cashierTransaction = CashierTransaction::create([
                'transaction_number' => $transaction_number,
                'transaction_date' => $validatedData['transaction_date'],
                'cashier_id' => $validatedData['cashier_id'],
                'customer_id' => $validatedData['customer_id'],
                'payment_method_id' => $validatedData['payment_method_id'],
                'discount' => $validatedData['discount'],
            ]);

            $cashierTransactionId = $cashierTransaction->id;
            // Create each cashier transaction item
            foreach ($validatedData['items'] as $item) {
                CashierTransactionItem::create([
                    'cashier_transaction_id' => $cashierTransaction->id,
                    'barang_id' => $item['barang_id'],
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
            'customer_id' => 'required|integer|exists:customers,id',
            'payment_method_id' => 'required|integer|exists:payment_methods,id',
            'discount' => 'required|numeric|min:0',
            'items' => 'required|array',
            'items.*.id' => 'nullable|integer|exists:cashier_transaction_items,id',
            'items.*.barang_id' => 'required|integer|exists:barangs,id',
            'items.*.transaction_type' => 'required|in:satuan,grosir,reseller',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($validatedData, $id) {
            // Find the cashier transaction
            $cashierTransaction = CashierTransaction::findOrFail($id);

            // Update the cashier transaction
            $updatedData = [
                'transaction_date' => $validatedData['transaction_date'],
                'cashier_id' => $validatedData['cashier_id'],
                'customer_id' => $validatedData['customer_id'],
                'payment_method_id' => $validatedData['payment_method_id'],
                'discount' => $validatedData['discount'],
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
                if (isset($item['id'])) {
                    // Update existing item
                    CashierTransactionItem::where('id', $item['id'])->update([
                        'barang_id' => $item['barang_id'],
                        'transaction_type' => $item['transaction_type'],
                        'qty' => $item['qty'],
                    ]);
                } else {
                    // Create new item
                    CashierTransactionItem::create([
                        'cashier_transaction_id' => $cashierTransaction->id,
                        'barang_id' => $item['barang_id'],
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
}
