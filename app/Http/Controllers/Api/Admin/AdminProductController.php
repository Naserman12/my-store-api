<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

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
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'quantity' => 'required|integer|min:0',
        'category_id' => 'required|exists:categories,id',
        'image' => 'nullable|image|max:2048',
        'features' => 'nullable|array'
    ]);

    $imagePath = null;

    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')
            ->store('products', 'public');
    }

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
        'image' => $imagePath,
    ]);


    // حفظ الميزات
    if ($request->features) {
        foreach ($request->features as $feature) {
            $product->features()->create([
                'name' => $feature
            ]);
        }
    }

    return response()->json($product, 201);
}
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
        $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'quantity' => 'required|integer|min:0',
        'category_id' => 'required|exists:categories,id',
        'image' => 'nullable|image|max:2048',
        'features' => 'nullable|array'
    ]);
    $product = Product::findOrFail($id);
    $imagePath = $product->image;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')
            ->store('products', 'public');
    }
    $product->update([
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
        'image' => $imagePath,
    ]);
    return response()->json($product);
}
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
