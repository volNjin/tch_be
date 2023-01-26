<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\AddressNote;
use App\Http\Controllers\Admin\ToppingController;

use Carbon\Carbon;

class OrderController extends Controller
{
    public function addOrder(Request $request){
        try{
            $order=Order::create([
                'user_id' => (int)$request->user_id,
                'user_name' => $request->user_name,
                'mobile_no' => $request->mobile_no,
                'order_date' => Carbon::now(),
                'state' => '0',
                'address' => $request->address,
                'note' => $request->note,
                'shipcost' => '15000',
                'total_price' => $request->total_price,
                'payment_method' => $request->payment_method,
            ]);
            if(!AddressNote::where('user_id', $request->user_id)
                            ->where('address', $request->address)
                            ->exists()){
                AddressNote::create([
                    'user_id' => (int)$request->user_id,
                    'user_name' => $request->user_name,
                    'address' => $request->address,
                    'mobile_no' => $request->mobile_no,
                ]);
            }
            foreach($request->products as $product){
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product['product_id'],
                    'product_count' => $product['product_count'],
                    'topping_id' => $product['topping_id'],
                    'topping_count' => $product['topping_count'],
                    'size' => $product['size'],
                    'price' =>$product['price'],
                ]);
            }
            return response([
                'error' => false
            ]);
        }catch(\Exception $err){
            return response([
                'error' => true,
                'message' => $err->getMessage()
            ]);
        };
    }

    public function getOrders(Request $request){
        $orders = Order::where('user_id', $request->user_id)
                        ->orderby('id')
                        ->get();
        return response([
            'orders' => $orders
        ]);
    }

    public function getOrderItems(Request $request){
        $orderItems = OrderItem::where('order_id', $request->order_id)
                                ->orderby('id')
                                ->get();
        
        foreach($orderItems as $item){
            $item->topping_id=ToppingController::getTopping($item->topping_id);
        }                     
        return response([
            'orderItems' => $orderItems
        ]);
    }
}
