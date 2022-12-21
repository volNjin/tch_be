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
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function create(Request $request){
        try {
            $category=Category::create([
                'name' => (string)$request->input('name'),
                'parent_id' => (int)$request->input('parent_id'),
                'description' => (string)$request->input('description'),
            ]);
            
            return response([
                'success'=> 'Tạo Danh Mục Thành Công',
                'category'=> $category,
            ]);
        } catch (\Exception $err) {
            return response([
                'error'=> $err->getMessage()
            ]);
        }
    }

    public function index(){
        $categories = Category::orderbyDesc('id')->paginate(20);
        return response ([
            'title' => 'Danh Sách Danh Mục Mới Nhất',
            'Categories' => $categories,
        ], 200);
    }

    public function update(Category $Category, CreateFormRequest $request){
        $this->CategoryService->update($request, $Category);

        return response([
            'message'=>'success',
            'Category' => $Category,
        ], 200);
    }

    public function destroy(Request $request): JsonResponse{
        $result = $this->CategoryService->destroy($request);
        if ($result) {
            return response()->json([
                'error' => false,
                'message' => 'Xóa thành công danh mục'
            ], 200);
        }

        return response()->json([
            'error' => true
        ], 500);
    }
}