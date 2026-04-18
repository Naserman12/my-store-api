<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Category;

class ProductController extends Controller
{
    /* ===============================
       GET PRODUCTS (STORE + DASHBOARD)
    =============================== */

    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['category','images'])
            ->where('is_hidden', false);

        /* ========= SEARCH ========= */

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        /* ========= CATEGORY FILTER ========= */

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        /* ========= FEATURED ========= */

        if ($request->featured) {
            $query->where('is_featured', true);
        }

        /* ========= SORT ========= */

        if ($request->sort == 'price_asc') {
            $query->orderBy('price');
        }

        if ($request->sort == 'price_desc') {
            $query->orderByDesc('price');
        }

        if ($request->sort == 'latest') {
            $query->latest();
        }

        $products = $query->paginate(12);

        return  ProductResource::collection($products);
    }
    /* ===============================
       SINGLE PRODUCT
    =============================== */
    public function show($id)
    {
        $product = Product::with([
            'category',
            'images',
            'features'
        ])->where('id',$id)->firstOrFail();

        return new ProductResource($product);
    }

    /* ===============================
       GET CATEGORIES WITH PRODUCTS
    =============================== */
    public function getCategoriesWithProducts()
    {
        $categories = Category::with('products')->get();
        return response()->json(['data' => ['categories' => $categories]]);
    }
    /* ===============================
       GET CATEGORIES
    =============================== */

    public function GetCategories(){
    $query = Category::query()->get()->all();
    return $query;
    }

    /* ==============================
     GET CATEGORY PRODUCTS
     ================================*/
     // Controller
public function getCategoryProducts($id)
{
    $category = Category::with(['products.images'])->findOrFail($id);

    return response()->json([
        'data' => [
            'products' => ProductResource::collection($category->products),
            'category_name' => $category->name,
        ]
    ]);
}
    /* ==============================
     GET CATEGORY
     ================================*/
    public function getCategory($id){
        return Category::where('id', $id)->firstOrFail();
    }
    /* ==============================
     Edit Product (Admin)
     ================================*/
     public function update(Request $request, $id)
     {
         $product = Product::findOrFail($id);
         $product->update($request->all());
         return new ProductResource($product);
     }
     /* ============================== */
}