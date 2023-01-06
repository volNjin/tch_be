<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'mobile_no',
        'order_date',
        'state',
        'address',
        'note',
        'shipcost',
        'payment_method',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}