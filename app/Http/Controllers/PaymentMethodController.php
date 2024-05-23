<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethods;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
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
        $payment_methods = PaymentMethods::all();
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
            'name' => 'required|string|max:255',
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
            'name' => 'required|string|max:255',
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
}
