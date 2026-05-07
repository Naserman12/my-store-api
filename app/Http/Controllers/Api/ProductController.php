<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\Wishlist;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use Illuminate\Support\Facades\DB;

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
        Show Last Added
    ================================ */
    public function lastAdded(){
        $product = Product::with([
            'category',
            'images',
            'features'
        ])->where('is_hidden', false)->take(5)->get();

        return $product;
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
    $category = Category::findOrFail($id);

    $products = $category->products()
        ->where('is_hidden', false)
        ->with('images')
        ->get();
    return response()->json([
        'data' => [
            'products' => ProductResource::collection($products),
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

     /* ==============================
     Upload images (Admin)
     ===============================*/

public function uploadImages(Request $request, $productId){
    $request->validate([
        'images.*' => 'required|image|max:2048'
    ]);

    $product = Product::findOrFail($productId);

        // إعداد Cloudinary
    Configuration::instance([
        'cloud' => [
            'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
            'api_key'    => env('CLOUDINARY_API_KEY'),
            'api_secret' => env('CLOUDINARY_API_SECRET'),
        ],
        'url' => [
            'secure' => true
        ]
    ]);
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

/* ==============================
     Toggle Wishlist
     ================================*/
public function toggle(Request $request)
{
    $user = $request->user();
    $productId = $request->product_id;

    $exists = Wishlist::where('user_id', $user->id)
        ->where('product_id', $productId)
        ->first();

    if ($exists) {
        $exists->delete();
        return response()->json(['status' => 'removed', 'product_id' => $productId]);
    }

    Wishlist::create([
        'user_id' => $user->id,
        'product_id' => $productId,

    ]);
    return response()->json(['status' => 'added', 'product_id' => $productId]);
}
/* ==============================
     Get Wishlist
     ================================*/
public function getWishlist(Request $request)
{
    $user = $request->user();

    $wishlist = Wishlist::where('user_id', $user->id)
        ->with('product.images')
        ->get();

    return response()->json(['data' => $wishlist]);
}

// الاعلى مبييعا
public function bestSelling()
{
$products = DB::table('products')
    ->leftJoin('order_items', 'order_items.product_id', '=', 'products.id')
    ->where('products.is_hidden', 0)
    ->select(
        'products.id',
        'products.name',
        'products.price',
        'products.image',
        DB::raw('COALESCE(SUM(order_items.quantity), 0) AS total_sold')
    )
    ->groupBy(
        'products.id',
        'products.name',
        'products.price',
        'products.image'
    )
    ->orderByDesc('total_sold')
    ->limit(5)
    ->get();
    return response()->json(['data' => $products]);
}


};