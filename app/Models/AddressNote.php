<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddressNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'address',
        'mobile_no'
    ];

    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
