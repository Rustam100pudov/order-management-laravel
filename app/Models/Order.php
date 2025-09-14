<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_inn',
        'company_name',
        'customer_address',
        'status',
        'operator_id'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'operator_id');
    }
}
