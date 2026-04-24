<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // Get all products (Public - for customers)
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
        
        // Search by name
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        
        // Sort by price
        if ($request->has('sort')) {
            if ($request->sort == 'price_asc') {
                $query->orderBy('price', 'asc');
            } elseif ($request->sort == 'price_desc') {
                $query->orderBy('price', 'desc');
            }
        } else {
            $query->latest();
        }
        
        $products = $query->paginate($request->get('per_page', 15));
        
        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Products retrieved successfully'
        ]);
    }

    // Get single product (Public)
    public function show($id)
    {
        $product = Product::with('hotel')->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $product,
            'message' => 'Product retrieved successfully'
        ]);
    }
    
    // Get products by hotel with categories (Public)
    public function getHotelMenu($hotelId)
    {
        $hotel = Hotel::findOrFail($hotelId);
        
        $products = Product::where('hotel_id', $hotelId)
            ->where('is_available', true)
            ->get()
            ->groupBy('category');
        
        return response()->json([
            'success' => true,
            'data' => [
                'hotel' => $hotel,
                'menu' => $products
            ],
            'message' => 'Hotel menu retrieved successfully'
        ]);
    }

    // Get hotel products for management (Hotel only)
    public function myProducts(Request $request)
    {
        $user = $request->user();
        
        if (!$user->hotelProfile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Hotel not found'
            ], 404);
        }
        
        $products = Product::where('hotel_id', $user->hotelProfile->id)
            ->orderBy('category')
            ->orderBy('name')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Your products retrieved successfully'
        ]);
    }

    // Create new product (Hotel only)
    public function store(Request $request)
{
    $request->validate([
        'name' => 'required',
        'price' => 'required',
        'category' => 'required',
        'preparation_time' => 'required'
    ]);

    $user = $request->user();

    if (!$user->hotelProfile) {
        return response()->json([
            'message' => 'Hotel profile not found'
        ], 404);
    }

    $product = Product::create([
        'hotel_id' => $user->hotelProfile->id,
        'name' => $request->name,
        'price' => $request->price,
        'category' => $request->category,
        'preparation_time' => $request->preparation_time,
        'description' => $request->description
    ]);

    return response()->json([
        'success' => true,
        'data' => $product
    ]);
}
    // Update product (Hotel only)
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $user = $request->user();

        // Check if product belongs to the hotel
        if ($product->hotel_id !== $user->hotelProfile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - This product does not belong to your hotel'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'sometimes|numeric|min:0',
            'preparation_time' => 'sometimes|integer|min:1|max:180',
            'category' => 'sometimes|string|max:100',
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

        // Check if product belongs to the hotel
        if ($product->hotel_id !== $user->hotelProfile->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized - This product does not belong to your hotel'
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

        if ($product->hotel_id !== $user->hotelProfile->id) {
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
            'message' => 'Product availability toggled successfully'
        ]);
    }

    // Bulk update products (Hotel only)
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.price' => 'sometimes|numeric|min:0',
            'products.*.is_available' => 'sometimes|boolean',
            'products.*.preparation_time' => 'sometimes|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $updated = [];
        $errors = [];

        foreach ($request->products as $productData) {
            $product = Product::find($productData['id']);
            
            // Check ownership
            if ($product->hotel_id !== $user->hotelProfile->id) {
                $errors[] = "Product ID {$product->id} does not belong to your hotel";
                continue;
            }
            
            $product->update($productData);
            $updated[] = $product;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'updated' => $updated,
                'errors' => $errors
            ],
            'message' => 'Bulk update completed'
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
            'data' => $categories,
            'message' => 'Categories retrieved successfully'
        ]);
    }

    // Get featured products (Public)
    public function getFeatured()
    {
        $products = Product::with('hotel')
            ->where('is_available', true)
            ->where('is_featured', true)
            ->limit(10)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $products,
            'message' => 'Featured products retrieved successfully'
        ]);
    }
}