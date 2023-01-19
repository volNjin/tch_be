<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topping;
use App\Models\ToppingProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ToppingController extends Controller{
    public static function getTopping($topping_id){
        $toppingList = collect();
        foreach($topping_id as $id){
            $topping= Topping::select('id', 'name', 'price')
                                ->find($id);
            $toppingList->push($topping);
        }
        return $toppingList;
    }
}