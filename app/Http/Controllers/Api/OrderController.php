<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items', 'operator']);


        // Фильтр по дате "с" и "по"
        if ($request->has('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->has('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }
        // Старый фильтр по одной дате (оставим для обратной совместимости)
        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhereHas('items', function ($q) use ($search) {
                      $q->where('product_name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($orders);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email',
            'customer_inn' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'customer_address' => 'nullable|string|max:500',
            'items' => 'required|array',
            'items.*.product_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'required|in:pieces,sets'
        ]);

        $order = Order::create([
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'customer_inn' => $request->customer_inn,
            'company_name' => $request->company_name,
            'customer_address' => $request->customer_address,
            'operator_id' => 1 // временно для теста API без авторизации
        ]);

        foreach ($request->items as $item) {
            $order->items()->create($item);
        }

        return response()->json($order->load('items'), 201);
    }

    public function statistics()
    {
        $statistics = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        return response()->json([
            'new' => $statistics['new'] ?? 0,
            'in_progress' => $statistics['in_progress'] ?? 0,
            'completed' => $statistics['completed'] ?? 0,
            'total' => $statistics->sum()
        ]);
    }
}
