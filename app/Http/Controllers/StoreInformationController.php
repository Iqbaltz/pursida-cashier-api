<?php

namespace App\Http\Controllers;

use App\Models\StoreInformation;
use Illuminate\Http\Request;

class StoreInformationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $store_data = StoreInformation::where('type', 'store_data')->first();
        return response()->json([
            'message' => 'data successfully retrieved',
            'data' => $store_data
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

        $store_data = StoreInformation::where('type', 'store_data')->first();
        if (!$store_data) {
            return response()->json([
                'error' => 'Store data not found, pease seed the data first',
            ], 404);
        }
        $store_data->name = $request->name;
        $store_data->address = $request->address;
        $store_data->phone_number = $request->phone_number;
        $store_data->save();
        return response()->json([
            'message' => 'Store data successfully updated',
            'data' => $store_data
        ]);
    }
}
