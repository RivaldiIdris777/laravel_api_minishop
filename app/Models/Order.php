<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_pesanan',
        'name',
        'email',
        'city',
        'address',
        'phone',
        'total_cost',
        'payment_method',
        'status',
    ];

    protected $casts = [
        'total_cost' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
