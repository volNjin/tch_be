<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\ToppingController;

use App\Models\Product;
use App\Models\Category;
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

    public function getChild($parent){
        $childList = Category::select('id')
                                ->where('parent_id', $parent->id) 
                                ->orderby('id')
                                ->get();
        return $childList;
    }

    public function indexByCategoryId(Request $request){   
        $categoryList = collect();
        $childList = collect();
        $productList = collect();

        $categoryList = Category::select('id')
                                ->where('id', $request->category_id)
                                ->get();
        for($i=0; $i<$categoryList->count();$i++){
            $childList = $this->getChild($categoryList[$i]);
            foreach($childList as $child){
                if(!($categoryList->contains($child)))
                $categoryList->push($child);
            }        
        }
        foreach($categoryList as $category){
            $product_list=Product::select('id', 'name', 'description', 'price', 'price_sale', 'image_url')
                        ->where('category_id', $category->id)    
                        ->where('active',1)
                        ->orderby('id')
                        ->get();
            foreach($product_list as $product){
                $productList->push($product);
            }
        }
        return response([
            'products' => $productList,
        ]);
    }

    

    public function getProductInfo(Request $request){
        $productInfo=Product::select('id', 'name', 'category_id', 'description', 'price', 'price_sale', 'image_url')
                        ->find($request->product_id);

        $toppingList=ToppingController::getToppingInfo($request);

        $toppings=$toppingList->getOriginalContent()['toppings'];

        $sameCategory = Product::select('id', 'name', 'category_id', 'description', 'price', 'price_sale', 'image_url')
                                ->where('category_id', $productInfo->category_id)
                                ->where('id', "<>", $request->product_id)
                                ->get();

        return response([
            'product' => $productInfo,
            'toppings' => $toppings,
            'same' => $sameCategory
        ]);
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