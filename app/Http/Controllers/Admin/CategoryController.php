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
    protected $CategoryService;

    public function __construct(CategoryService $CategoryService){
        $this->CategoryService = $CategoryService;
    }

    public function create(){
        return view('admin.Category.add', [
            'title' => 'Thêm Danh Mục Mới',
            'categories' => $this->CategoryService->getParent()
        ]);
    }

    public function store(CreateFormRequest $request){
        $this->CategoryService->create($request);

        return redirect()->back();
    }

    public function index(){
        return view('admin.Category.list', [
            'title' => 'Danh Sách Danh Mục Mới Nhất',
            'Categorys' => $this->CategoryService->getAll()
        ]);
    }

    public function show(Category $Category){
        return view('admin.Category.edit', [
            'title' => 'Chỉnh Sửa Danh Mục: ' . $Category->name,
            'Category' => $Category,
        ]);
    }

    public function update(Category $Category, CreateFormRequest $request){
        $this->CategoryService->update($request, $Category);

        return redirect('/admin/Categories/list');
    }

    public function destroy(Request $request): JsonResponse{
        $result = $this->CategoryService->destroy($request);
        if ($result) {
            return response()->json([
                'error' => false,
                'message' => 'Xóa thành công danh mục'
            ]);
        }

        return response()->json([
            'error' => true
        ]);
    }
}