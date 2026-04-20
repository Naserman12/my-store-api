<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\ProductImage;
use Cloudinary\Api\Upload\UploadApi;

class ProductController extends Controller
{
    /* ===============================
       GET PRODUCTS (STORE + DASHBOARD)
    =============================== */

    public function index(Request $request)
    {
        $query = Product::query()
            ->with(['category','images',])
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
        $categories = Category::with('products', 'products.images')->get();
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
public function getCategoryProducts($id){
    $category = Category::with(['products' ,'products.images'])->findOrFail($id);

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


public function uploadImages(Request $request, $productId){
    $request->validate([
        'images.*' => 'required|image|max:2048'
    ]);

    $product = Product::findOrFail($productId);

    foreach ($request->file('images') as $index => $file) {

        $result = (new UploadApi())->upload(
            $file->getRealPath(),
            [
                'folder' => 'products',
                'transformation' => [
                    'width' => 600,
                    'height' => 600,
                    'crop' => 'fill',
                    'quality' => 'auto'
                ]
            ]
        );

        ProductImage::create([
            'product_id' => $product->id,
            'image_url' => $result['secure_url'],
            'public_id' => $result['public_id'],
            'is_primary' => $index === 0 // أول صورة رئيسية
        ]);
    }

    return response()->json([
        'message' => 'تم رفع الصور بنجاح'
    ]);
}
}