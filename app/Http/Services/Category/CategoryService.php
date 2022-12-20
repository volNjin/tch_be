<?php


namespace App\Http\Services\Category;


use App\Models\Category;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CategoryService
{
    public function getParent()
    {
        return Category::where('parent_id', 0)->get();
    }

    public function show()
    {
        return Category::select('name', 'id')
            ->where('parent_id', 0)
            ->orderbyDesc('id')
            ->get();
    }

    public function getAll()
    {
        return Category::orderbyDesc('id')->paginate(20);
    }

    public function create($request)
    {
        try {
            Category::create([
                'name' => (string)$request->input('name'),
                'parent_id' => (int)$request->input('parent_id'),
                'description' => (string)$request->input('description'),
            ]);

            Session::flash('success', 'Tạo Danh Mục Thành Công');
        } catch (\Exception $err) {
            Session::flash('error', $err->getMessage());
            return false;
        }

        return true;
    }

    public function update($request, $Category): bool
    {
        if ($request->input('parent_id') != $Category->id) {
            $Category->parent_id = (int)$request->input('parent_id');
        }

        $Category->name = (string)$request->input('name');
        $Category->description = (string)$request->input('description');
        $Category->save();

        Session::flash('success', 'Cập nhật thành công Danh mục');
        return true;
    }

    public function destroy($request)
    {
        $id = (int)$request->input('id');
        $Category = Category::where('id', $id)->first();
        if ($Category) {
            return Category::where('id', $id)->orWhere('parent_id', $id)->delete();
        }
        return false;
    }


    public function getId($id)
    {
        return Category::where('id', $id)->firstOrFail();
    }

    public function getProduct($Category, $request)
    {
        $query = $Category->products()
            ->select('id', 'name', 'price', 'price_sale', 'thumb');

        if ($request->input('price_sale')) {
            $query->orderBy('price_sale', $request->input('price_sale'));
        }

        return $query
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();
    }
}