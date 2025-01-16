<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function records()
    {
        try {
            $customers = Customer::select('identification_number', 'dv', 'name', 'phone', 'address', 'email')->get();
            return response()->json([
                'success' => true,
                'data' => $customers
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
