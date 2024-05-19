<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function __invoke(Request $request)
    {
        $customers = Customer::all();
        return response()->json([
            'message' => 'data successfully retrieved',
            'data' => $customers
        ]);
    }

    public function detail(Request $request, $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                'error' => 'Customer not found',
            ], 404);
        }
        return response()->json([
            'message' => 'data successfully retrieved',
            'data' => $customer
        ]);
    }

    public function insert(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
        ]);

        $newCustomer = Customer::create($validatedData);
        return response()->json([
            'message' => 'Customer successfully added',
            'data' => $newCustomer
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
        ]);

        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                'error' => 'customer not found',
            ], 404);
        }
        $customer->name = $request->name;
        $customer->address = $request->address;
        $customer->phone_number = $request->phone_number;
        $customer->save();
        return response()->json([
            'message' => 'Customer successfully updated',
            'data' => $customer
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $customer = Customer::find($id);
        if (!$customer) {
            return response()->json([
                'error' => 'Customer not found',
            ], 404);
        }
        $customer->delete();
        return response()->json([
            'message' => 'Customer successfully deleted',
            'data' => $customer
        ]);
    }
}
