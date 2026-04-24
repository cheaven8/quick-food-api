<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * 1. CREATE ORDER (Customer)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'hotel_id' => 'required|exists:hotel_profiles,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1|max:99',
            'delivery_address' => 'required|string|max:500',
            'customer_phone' => 'required|string|max:20',
            'special_instructions' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        $subtotal = 0;
        $orderItems = [];

        foreach ($request->items as $item) {

            $product = Product::find($item['product_id']);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            // ensure product belongs to hotel
            if ($product->hotel_id != $request->hotel_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product does not belong to this hotel'
                ], 422);
            }

            if (!$product->is_available) {
                return response()->json([
                    'success' => false,
                    'message' => "Product {$product->name} is not available"
                ], 422);
            }

            $total = $product->price * $item['quantity'];
            $subtotal += $total;

            $orderItems[] = [
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
                'total' => $total,
            ];
        }

        $deliveryFee = 50;
        $totalAmount = $subtotal + $deliveryFee;

        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'customer_id' => $user->id,
            'hotel_id' => $request->hotel_id,
            'status' => 'pending',
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total' => $totalAmount,
            'delivery_address' => $request->delivery_address,
            'customer_phone' => $request->customer_phone,
            'special_instructions' => $request->special_instructions,
        ]);

        foreach ($orderItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['total'],
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $order->load('items.product'),
            'message' => 'Order created successfully'
        ], 201);
    }

    /**
     * 2. CUSTOMER ORDERS
     */
    public function myOrders(Request $request)
    {
        $orders = Order::where('customer_id', $request->user()->id)
            ->with('hotel', 'items.product')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * 3. HOTEL ORDERS
     */
    public function hotelOrders(Request $request)
    {
        $hotel = $request->user()->hotelProfile;

        if (!$hotel) {
            return response()->json([
                'success' => false,
                'message' => 'Hotel profile not found'
            ], 404);
        }

        $orders = Order::where('hotel_id', $hotel->id)
            ->with('customer', 'items.product')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * 4. SHOW SINGLE ORDER
     */
    public function show(Request $request, $id)
    {
        $order = Order::with('customer', 'hotel', 'items.product')->findOrFail($id);

        $user = $request->user();

        if (
            $order->customer_id !== $user->id &&
            optional($user->hotelProfile)->id !== $order->hotel_id &&
            !$user->is_admin
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $order
        ]);
    }

    /**
     * 5. UPDATE STATUS (Hotel/Admin)
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,confirmed,preparing,ready_for_delivery,delivered,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $order = Order::findOrFail($id);
        $user = $request->user();

        if (
            !$user->is_admin &&
            optional($user->hotelProfile)->id !== $order->hotel_id
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $order->status = $request->status;

        if ($request->status === 'delivered') {
            $order->delivered_at = now();
        }

        $order->save();

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order status updated'
        ]);
    }

    /**
     * 6. CANCEL ORDER
     */
    public function cancelOrder(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        if ($order->customer_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled'
            ], 422);
        }

        $order->status = 'cancelled';
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled'
        ]);
    }
}