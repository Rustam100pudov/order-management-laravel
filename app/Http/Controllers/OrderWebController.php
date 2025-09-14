<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderWebController extends Controller
{
    public function create()
    {
        return view('orders.create');
    }

    public function index()
    {
        return view('orders.index');
    }
}
