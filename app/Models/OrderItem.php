<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'topping_id',
        'size',
        'price'
    ];

    protected $casts = [
        'topping_id' =>'array'
    ];
    
    public function order(){
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function product(){
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
