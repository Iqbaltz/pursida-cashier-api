<?php

namespace App\Http\Controllers;

use App\Exports\PaymentMethodExport;
use App\Models\PaymentMethods;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PaymentMethodController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $payment_methods = PaymentMethods::orderBy('created_at', 'desc')->get();
        return response()->json([
            'message' => 'data successfully retrieved',
            'data' => $payment_methods
        ]);
    }

    public function detail(Request $request, $id)
    {
        $payment_method = PaymentMethods::find($id);
        if (!$payment_method) {
            return response()->json([
                'error' => 'Payment Method not found',
            ], 404);
        }
        return response()->json([
            'message' => 'data successfully retrieved',
            'data' => $payment_method
        ]);
    }

    public function insert(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:payment_mehods,name|string|max:255',
        ]);

        $new_payment_method = PaymentMethods::create($validatedData);
        return response()->json([
            'message' => 'Payment Method successfully added',
            'data' => $new_payment_method
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:payment_methods,name,' . $id . '|string|max:255',
        ]);

        $payment_method = PaymentMethods::find($id);
        if (!$payment_method) {
            return response()->json([
                'error' => 'Payment Method not found',
            ], 404);
        }
        $payment_method->name = $request->name;
        $payment_method->save();
        return response()->json([
            'message' => 'Payment Method successfully updated',
            'data' => $payment_method
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $payment_method = PaymentMethods::find($id);
        if (!$payment_method) {
            return response()->json([
                'error' => 'Payment Method not found',
            ], 404);
        }
        $payment_method->delete();
        return response()->json([
            'message' => 'Payment Method successfully deleted',
            'data' => $payment_method
        ]);
    }
    public function export_excel(Request $request)
    {
        $date = get_indo_date(date('Y-m-d'));
        $filename = "Daftar Metode Pembayaran - {$date}.xlsx";
        return Excel::download(new PaymentMethodExport, $filename);
    }
}
