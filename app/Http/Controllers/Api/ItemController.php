<?php

namespace App\Http\Controllers\Api;

use App\Item;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function records()
    {
        $items = Item::all();
        return compact('items');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:500',
            'second_name' => 'nullable|string|max:500',
            'description' => 'nullable|string|max:500',
            'item_type_id' => 'required|string|max:2',
            'internal_id' => 'nullable|string|max:100',
            'date_of_due' => 'nullable|date',
            'sale_unit_price' => 'required|numeric',
            'purchase_unit_price' => 'nullable|numeric',
            'commission_amount' => 'nullable|numeric',
            'commission_type' => 'nullable|string|max:255',
            'amount_plastic_bag_taxes' => 'nullable|numeric',
            'calculate_quantity' => 'nullable|boolean',
            'sale_unit_price_set' => 'nullable|numeric',
            'is_set' => 'nullable|boolean',
            'model' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:255',
            'image_medium' => 'nullable|string|max:255',
            'image_small' => 'nullable|string|max:255',
            'stock' => 'nullable|numeric',
            'stock_min' => 'nullable|numeric',
            'lot_code' => 'nullable|string|max:255',
            'lots_enabled' => 'nullable|boolean',
            'series_enabled' => 'nullable|boolean',
            'percentage_of_profit' => 'nullable|numeric',
            'has_perception' => 'nullable|boolean',
            'percentage_perception' => 'nullable|numeric',
            'attributes' => 'nullable|json',
            'active' => 'nullable|boolean',
            'status' => 'nullable|boolean',
            'apply_store' => 'nullable|boolean',
        ]);

        $item = Item::create($validatedData);

        return response()->json($item, 201);
    }
}
