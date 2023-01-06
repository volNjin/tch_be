<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Topping;
use App\Models\ToppingProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {   
        $productList = Product::select('id', 'name', 'category_id', 'description', 'price', 'price_sale', 'image_url')
                        ->where('active',1)
                        ->orderby('id')
                        ->get();
        return response([
            'products' => $productList,
        ]);
    }

    public function indexByCategoryId(Request $request){   
        $productList = Product::select('id', 'name', 'description', 'price', 'price_sale', 'image_url')
                        ->where('category_id', $request->category_id)    
                        ->where('active',1)
                        ->orderby('id')
                        ->get();
        return response([
            'products' => $productList,
        ]);
    }

    public function getProductInfo(Request $request){
        $product=Product::select('name', 'description', 'price', 'price_sale', 'image_url')
                        ->find($request->product_id);
        return response($product);
    }
    
    public function create(Request $request){
            if(Product::where('name',$request->name)->first()){
                return response([
                    'message' => 'Đã có sản phẩm này'
                ]);
            }
            if($this->isValidPrice($request)){
                $product=Product::create($request->all());
                return response ([
                    'message' => 'Thêm sản phẩm mới thành công',
                    'product' => $product,
                ], 200);
            }   
    }

    public function update(Request $request)
    {
        $product=Product::where('id', $request->input('id'))->first();
        $isValidPrice = $this->isValidPrice($request);
        if ($isValidPrice === false) return false;

        try {
            $product->fill($request->input());
            $product->save();
            return response([
                'error' => false,
                'product'=> $product
            ],200);
        }catch (\Exception $err) {
            return response([
                'error'=> $err->getMessage()
            ], 500);
        }
    }


    public function destroy(Request $request)
    {
        $product = Product::where('id', $request->input('id'))->first();
        if ($product) {
            $product->delete();
            return response()->json([
                'message' => 'Xóa thành công sản phẩm'
            ], 200);
        }
        else return response()->json([ 'message' => 'Không có sản phẩm này trong dữ liệu' ]);
    }

    protected function isValidPrice($request)
    {
        if ($request->input('price') != 0 && $request->input('price_sale') != 0
            && $request->input('price_sale') > $request->input('price')
        ) {
            Session::flash('error', 'Giá giảm phải nhỏ hơn giá gốc');
            return false;
        }

        if ($request->input('price_sale') != 0 && (int)$request->input('price') == 0) {
            Session::flash('error', 'Vui lòng nhập giá gốc');
            return false;
        }

        return  true;
    }
}