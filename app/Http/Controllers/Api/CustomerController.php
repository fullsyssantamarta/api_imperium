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

    public function store(Request $request)
    {
       try{

        $validatedData = $request->validate([
            'identification_number' => 'required|string|max:20|unique:customers',
            'dv' => 'nullable|string|max:1',
            'name' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'email' => 'nullable|email|max:255',
            'password' => 'required|string|min:6'
        ]);

        $validatedData['password'] = bcrypt($validatedData['password']);
        $customer = Customer::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Cliente creado exitosamente',
            'data' => $customer
        ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
