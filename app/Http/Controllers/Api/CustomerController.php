<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        $orders = Order::where('customer_phone', 'like', '%' . $request->phone . '%')
            ->select('customer_name', 'customer_phone', 'customer_email', 'company_name', 'customer_address')
            ->distinct()
            ->get();

        return response()->json($orders);
    }
}
