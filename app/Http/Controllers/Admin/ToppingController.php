<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topping;
use App\Models\ToppingProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ToppingController extends Controller{
    public function getToppingInfo(Request $request){
        try{
            $topping_list = ToppingProduct::select('topping_id')
                                    ->find($request->product_id);
            $toppingList = $this->getTopping($topping_list);
            return response([
                'toppings' => $toppingList,
            ]);
        } catch(\Exception $err){
            return response([
                'message' => $err->getMessage()
            ]);
        };
    }

    public function getTopping($topping_list){
        $toppingList = collect();
        foreach($topping_list['topping_id'] as $topping_id){
            $topping= Topping::select('name', 'price')
                                ->find($topping_id);
            $toppingList->push($topping);
        }
        return $toppingList;
    }
}