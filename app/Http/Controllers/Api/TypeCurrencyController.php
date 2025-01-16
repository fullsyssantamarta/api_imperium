<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\TypeCurrency;
use Illuminate\Http\Request;

class TypeCurrencyController extends Controller
{
    public function records()
    {
        try {
            $currencies = TypeCurrency::select('id','name', 'code')->get();
            return response()->json([
                'success' => true,
                'data' => $currencies
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
