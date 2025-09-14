<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function create()
    {
        // Здесь можно вернуть view('operator') или другую форму заказа
        return view('operator');
    }

    public function store(Request $request)
    {
        // Логика сохранения заказа
        // ...
        return redirect()->route('orders.create')->with('success', 'Заказ создан!');
    }
}
