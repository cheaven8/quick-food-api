// app/Http/Controllers/ProductController.php
<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // List all products (Customer view)
    public function index(Request $request)
    {
        $query = Product::with('hotel')->where('is_available', true);
        
        // Filter by hotel
        if ($request->has('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }
        
        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        // Filter by featured
        if ($request->has('featured') && $request->featured) {
            $query->featured();
        }
        
        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        $products = $query->latest()->paginate(15);
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    // View single product
    public function show($id)
    {
        $product = Product::with('hotel')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    // Get hotel's own products (for hotel management)
    public function myProducts(Request $request)
    {
        $user = $request->user();
        $products = Product::where('hotel_id', $user->hotel_id)->get();
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    // Add new product (Hotel only)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'preparation_time' => 'required|integer|min:1|max:180',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'boolean',
            'ingredients' => 'nullable|string',
            'calories' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }
        
        $product = Product::create([
            'hotel_id' => $user->hotel_id,
            'name' => $request->name,
            'price' => $request->price,
            'category' => $request->category,
            'preparation_time' => $request->preparation_time,
            'description' => $request->description,
            'image' => $imagePath,
            'is_available' => true,
            'is_featured' => $request->is_featured ?? false,
            'ingredients' => $request->ingredients,
            'calories' => $request->calories,
        ]);

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Product created successfully'
        ], 201);
    }

    // Update product (Hotel only)
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $user = $request->user();
        
        // Check if product belongs to this hotel
        if ($product->hotel_id !== $user->hotel_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - This product does not belong to your hotel'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'category' => 'sometimes|string|max:100',
            'preparation_time' => 'sometimes|integer|min:1|max:180',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'ingredients' => 'nullable|string',
            'calories' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        // Update product fields
        $product->fill($request->except('image'));
        $product->save();

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Product updated successfully'
        ]);
    }

    // Delete product (Hotel only)
    public function destroy(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $user = $request->user();
        
        // Check if product belongs to this hotel
        if ($product->hotel_id !== $user->hotel_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        // Delete image if exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    // Toggle product availability (Hotel only)
    public function toggleAvailability(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $user = $request->user();
        
        if ($product->hotel_id !== $user->hotel_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $product->is_available = !$product->is_available;
        $product->save();

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Product availability updated'
        ]);
    }

    // Toggle featured product (Hotel only)
    public function toggleFeatured(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $user = $request->user();
        
        if ($product->hotel_id !== $user->hotel_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $product->is_featured = !$product->is_featured;
        $product->save();

        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Product featured status updated'
        ]);
    }

    // Get featured products (Public)
    public function getFeatured()
    {
        $products = Product::with('hotel')
            ->available()
            ->featured()
            ->latest()
            ->take(10)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    // Get product categories (Public)
    public function getCategories()
    {
        $categories = Product::where('is_available', true)
            ->distinct()
            ->pluck('category');
        
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}