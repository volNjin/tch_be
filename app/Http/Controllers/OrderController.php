<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
class OrderController extends Controller
{
    public function addOrder(Request $request){
        try{
            $order=Order::create([
                'user_id' => (int)$request->user_id,
                'user_name' => $request->user_name,
                'mobile_no' => $request->mobile_no,
                'order_date' => $request->order_date,
                'state' => '0',
                'address' => $request->address,
                'note' => $request->note,
                'shipcost' => '15000',
                'payment_method' => $request->payment_method,
            ]);
            // dd($order->id);
            foreach($request->products as $product){
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product['product_id'],
                    'topping_id' => $product['topping_id'],
                    'size' => $product['size'],
                    'price' =>$product['price'],
                ]);
            }
        }catch(\Exception $err){
            return response([
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
        return response([
            'orderItems' => $orderItems
        ]);
    }
}
