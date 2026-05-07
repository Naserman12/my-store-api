<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;
use App\Models\ProductImage;
class AdminProductController extends Controller
{
    /* ==============================
     show All Products (Admin)
     ================================*/
    public function index(){
        $products = Product::with('category')->get()->all();
        return response()->json($products);
    }
    /* ==============================
     Create Product (Admin)
     ================================*/
     public function store(Request $request)
{
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
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'quantity' => 'required|integer|min:0',
        'category_id' => 'required|exists:categories,id',
        'images.*' => 'nullable|image|max:2048',
        'features' => 'nullable|array'
    ]);

    $product = Product::create([
        'name' => $request->name,
        'slug' => $request->slug,
        'description' => $request->description,
        'price' => $request->price,
        'sale_price' => $request->sale_price,
        'sku' => $request->sku,
        'quantity' => $request->quantity,
        'category_id' => $request->category_id,
        'is_featured' => $request->is_featured,
        'is_hidden' => $request->is_hidden,
    ]);
    // رفع الصور
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $file) {
             $result = (new UploadApi())->upload(
                $request->file('images')->getRealPath(),
                [
                    'folder' => 'images',
                    'transformation' => [
                    'width' => 300,
                    'height' => 300,
                    'crop' => 'fill',
                    'quality' => 'auto',
                    'fetch_format' => 'auto'
                ]
                ]
            );
            // $uploaded = Cloudinary::upload(
            //     $file->getRealPath(),
            //     [
            //         'folder' => 'products'
            //     ]
            // );

            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => $result['secure_url'],
                'public_id' => $result['public_id'],
                'is_primary' => $index === 0
            ]);
        }
    }

    // حفظ الميزات
    if ($request->features) {

        foreach ($request->features as $feature) {

            $product->features()->create([
                'name' => $feature
            ]);

        }

    }

    return response()->json($product->load('images'), 201);
}
//     public function store(Request $request)
// {
//     $request->validate([
//         'name' => 'required|string|max:255',
//         'description' => 'nullable|string',
//         'quantity' => 'required|integer|min:0',
//         'category_id' => 'required|exists:categories,id',
//         'image' => 'nullable|image|max:2048',
//         'features' => 'nullable|array'
//     ]);

//     $imagePath = null;

//     if ($request->hasFile('image')) {
//         $imagePath = $request->file('image')
//             ->store('products', 'public');
//     }
//     $product = Product::create([
//         'name' => $request->name,
//         'slug' => $request->slug,
//         'description' => $request->description,
//         'price' => $request->price,
//         'sale_price' => $request->sale_price,
//         'sku' => $request->sku,
//         'quantity' => $request->quantity,
//         'category_id' => $request->category_id,
//         'is_featured' => $request->is_featured,
//         'is_hidden' => $request->is_hidden,
//         'image' => $imagePath,
//     ]);
//     // حفظ الميزات
//     if ($request->features) {
//         foreach ($request->features as $feature) {
//             $product->features()->create([
//                 'name' => $feature
//             ]);
//         }
//     }

//     return response()->json($product, 201);
// }
/* ==============================
    Update Hidden Status (Admin)
    ================================*/
public function updateHiddenStatus(Request $request, $id)
{
    $request->validate([
        'is_hidden' => 'required|boolean',
    ]);
    $product = Product::findOrFail($id);
    $product->update([
        'is_hidden' => $request->is_hidden,
    ]);
    return response()->json($product);
}


/* ==============================
    Update Product (Admin)
    ================================*/
    public function update(Request $request, $id)
{
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
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'quantity' => 'required|integer|min:0',
        'category_id' => 'required|exists:categories,id',
        'images.*' => 'nullable|image|max:2048',
        'features' => 'nullable|array'
    ]);

    $product = Product::with('images')->findOrFail($id);
    // تحديث البيانات بدون الصور
    $product->update($request->except('images'));
    // // تحديث البيانات
    // $product->update([
    //     'name' => $request->name,
    //     'slug' => $request->slug,
    //     'description' => $request->description,
    //     'price' => $request->price,
    //     'sale_price' => $request->sale_price,
    //     'sku' => $request->sku,
    //     'quantity' => $request->quantity,
    //     'category_id' => $request->category_id,
    //     'is_featured' => $request->is_featured,
    //     'is_hidden' => $request->is_hidden,
    // ]);

       // رفع الصور
    if ($request->hasFile('images')) {
        foreach($request->images as $img){
            if($img->public_id){
                (new UploadApi())->destroy($img->public_id);
                $img->delete();
            }
        }
        // رفع الصور الجديدة
        foreach ($request->file('images') as $index => $file) {
             $result = (new UploadApi())->upload(
                $request->file('images')->getRealPath(),
                [
                    'folder' => 'images',
                    'transformation' => [
                    'width' => 800,
                    'height' => 800,
                    'crop' => 'fill',
                    'quality' => 'auto',
                    'fetch_format' => 'auto'
                ]
                ]
            );
            ProductImage::create([
                'product_id' => $product->id,
                'image_url' => $result['secure_url'],
                'public_id' => $result['public_id'],
                'is_primary' => $index === 0
            ]);
        }
    }

    // إذا تم رفع صور جديدة
    // if ($request->hasFile('images')) {

    //     // حذف الصور القديمة من Cloudinary
    //     foreach ($product->images as $image) {

    //         if ($image->public_id) {

    //             Cloudinary::destroy($image->public_id);

    //         }

    //         $image->delete();
    //     }

    //     // رفع الصور الجديدة
    //     foreach ($request->file('images') as $index => $file) {

    //         $uploaded = Cloudinary::upload(
    //             $file->getRealPath(),
    //             [
    //                 'folder' => 'products'
    //             ]
    //         );

    //         ProductImage::create([
    //             'product_id' => $product->id,
    //             'image_url' => $uploaded->getSecurePath(),
    //             'public_id' => $uploaded->getPublicId(),
    //             'is_primary' => $index === 0
    //         ]);
    //     }
    // }

    return response()->json(
        $product->load('images')
    );
}
// public function update(Request $request, $id)
// {
//         $request->validate([
//         'name' => 'required|string|max:255',
//         'description' => 'nullable|string',
//         'quantity' => 'required|integer|min:0',
//         'category_id' => 'required|exists:categories,id',
//         'images' => 'nullable|image|max:2048',
//         'features' => 'nullable|array'
//     ]);
//     $product = Product::findOrFail($id);
//     $imagePath = $product->image;
//     if ($request->hasFile('image')) {
//         $imagePath = $request->file('image')
//             ->store('products', 'public');
//     }
//     $product->update([
//         'name' => $request->name,
//         'slug' => $request->slug,
//         'description' => $request->description,
//         'price' => $request->price,
//         'sale_price' => $request->sale_price,
//         'sku' => $request->sku,
//         'quantity' => $request->quantity,
//         'category_id' => $request->category_id,
//         'is_featured' => $request->is_featured,
//         'is_hidden' => $request->is_hidden,
//         'images' => $imagePath,
//     ]);
//     return response()->json($product);
// }
/* ==============================
    Delete Product (Admin)
    ================================*/
public function destroy($id)
{
    $product = Product::findOrFail($id);
    $product->delete();
    return response()->json(['message' => 'Product deleted successfully']);
}
}
