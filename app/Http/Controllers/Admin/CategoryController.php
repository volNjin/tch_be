<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CreateFormRequest;
use App\Http\Services\Category\CategoryService;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function create(Request $request){
        if(Category::where('name', $request->name)->first()){
            return response([
                'message' => 'Đã có danh mục này'
            ]);
        }
            $category=Category::create([
                'name' => (string)$request->input('name'),
                'parent_id' => (int)$request->input('parent_id'),
            ]);
            
            return response([
                'error' => 'Thêm danh mục thành công',
                'category'=> $category,
            ]);
    }

    public function index(){
        $categories = Category::orderby('id')->get();
        return response ([
            'Categories' => $categories,
        ], 200);
    }

    public function indexByParentId(Request $request){
        $categories = Category::where('parent_id', $request->parent_id)
                                ->orderby('id')->get();
        return response ([
            'Categories' => $categories,
        ], 200);
    }
    public function update(Request $request){
        $category=Category::find($request->id);
        if ($request->input('parent_id')!=NULL && $request->input('parent_id') != $category->id) {
            $category->parent_id = (int)$request->input('parent_id');
        }

        $category->name = (string)$request->input('name');
        $category->save();
        return response([
            'message' => 'Cập nhật thành công',
            'Category' => $category,
        ], 200);
    }

    public function destroy(Request $request): JsonResponse{
        $id = (int)$request->input('id');
        $Category = Category::where('id', $id)->first();
        if ($Category) {
            $result = Category::where('id', $id)->orWhere('parent_id', $id)->delete();
        }
        if ($result) {
            return response()->json([
                'message' => 'Xóa thành công danh mục'
            ], 200);
        }

        return response()->json([
            'message' => 'Xóa thành công không danh mục'
        ], 500);
    }
}