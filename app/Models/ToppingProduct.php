<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToppingProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'topping_id',
        'product_id'
    ];

    protected $casts = [
        'topping_id' =>'array'
    ];
    
    public function product(){
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function topping(){
        return $this->hasOne(Topping::class, 'id', 'topping_id');
    }
}
