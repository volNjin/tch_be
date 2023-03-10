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
        $productList = Product::where('active', 1)
                            ->orderby('id')
                            ->get();
        return response([
            'products' => $productList,
        ]);
    }

    public function getChild($parent)
    {
        $childList = Category::select('id')
            ->where('parent_id', $parent->id)
            ->orderby('id')
            ->get();
        return $childList;
    }

    public function indexByCategoryId(Request $request)
    {
        $categoryList = collect();
        $childList = collect();
        $productList = collect();

        $categoryList = Category::select('id')
            ->where('id', $request->category_id)
            ->get();
        for ($i = 0; $i < $categoryList->count(); $i++) {
            $childList = $this->getChild($categoryList[$i]);
            foreach ($childList as $child) {
                if (!($categoryList->contains($child)))
                    $categoryList->push($child);
            }
        }
        foreach ($categoryList as $category) {
            $product_list = Product::where('category_id', $category->id)
                                    ->where('active', 1)
                                    ->orderby('id')
                                    ->get();
            foreach ($product_list as $product) {
                $productList->push($product);
            }
        }
        return response([
            'products' => $productList,
        ]);
    }



    public function getProductInfo(Request $request)
    {
        $productInfo = Product::select(
            'id',
            'name',
            'category_id',
            'description',
            'price',
            'price_sale',
            'image_url'
        )
            ->find($request->product_id);

        $toppingList = $this->getToppingInfo($request->product_id);

        $toppings = $toppingList->getOriginalContent()['toppings'];

        $sameCategory = Product::select('id', 'name', 'category_id', 'description', 'price', 'price_sale', 'image_url')
            ->where('category_id', $productInfo->category_id)
            ->where('id', "<>", $request->product_id)
            ->where('active', 1)
            ->get();

        return response([
            'product' => $productInfo,
            'toppings' => $toppings,
            'same' => $sameCategory
        ]);
    }

    public function getToppingInfo($product_id)
    {
        try {
            $topping_list = ToppingProduct::select('topping_id')
                ->find($product_id);
            $toppingList = ToppingController::getTopping($topping_list->topping_id);
            return response([
                'toppings' => $toppingList,
            ]);
        } catch (\Exception $err) {
            return response([
                'message' => $err->getMessage()
            ]);
        };
    }
    public function create(Request $request)
    {
        if (Product::where('name', $request->name)->first()) {
            return response([
                'message' => '???? c?? s???n ph???m n??y'
            ]);
        }
            $product = Product::create([
                'name' => $request->name,
                'category_id' =>$request->category_id,
                'description' => $request->description,
                'price' => $request->price,
                'price_sale'=> $request->price_sale,
                'active' => 1,
                'image_url' => $request->image_url
            ]);
            ToppingProduct::create([
                'product_id' => $product->id,
                'topping_id' => [1,2,3,4,5],
            ]);
            return response([
                'message' => "Th??m s???n ph???m th??nh c??ng",
                'product' => $product,
            ], 200);
    }

    public function update(Request $request)
    {
        $product = Product::where('id', $request->input('id'))->first();
        $isValidPrice = $this->isValidPrice($request);
        if ($isValidPrice === false) return false;

        try {
            $product->fill($request->input());
            $product->save();
            return response([
                'message' => 'C???p nh???t th??nh c??ng',
                'product' => $product
            ], 200);
        } catch (\Exception $err) {
            return response([
                'error' => $err->getMessage()
            ], 500);
        }
    }


    public function destroy(Request $request)
    {
        $product = Product::where('id', $request->input('id'))->first();
        if ($product) {
            $product->delete();
            return response()->json([
                'message' => 'X??a th??nh c??ng s???n ph???m'
            ], 200);
        } else return response([
            'message' => 'Kh??ng c?? s???n ph???m n??y trong d??? li???u'
        ], 500);
    }

    protected function isValidPrice($request)
    {
        if (
            $request->input('price') != 0 && $request->input('price_sale') != 0
            && $request->input('price_sale') > $request->input('price')
        ) {
            Session::flash('error', 'Gi?? gi???m ph???i nh??? h??n gi?? g???c');
            return false;
        }

        if ($request->input('price_sale') != 0 && (int)$request->input('price') == 0) {
            Session::flash('error', 'Vui l??ng nh???p gi?? g???c');
            return false;
        }

        return  true;
    }
}
