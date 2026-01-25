<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function showLengthOrderToday()
    {
        $count = Order::whereDate('created_at', Carbon::today())->count();

        return response()->json([
            'date'  => Carbon::today()->toDateString(),
            'count' => $count
        ]);
    }

    public function showOrders()
    {
        try {
        $orders = Order::with('items')
            ->orderBy('created_at', 'desc')
            ->get();

        // Jika data kosong
        if ($orders->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Order data is empty',
                'orders'  => []
            ], 200);
        }

        // Jika data ada
        return response()->json([
            'success' => true,
            'message' => 'Order retrieved successfully',
            'orders'  => $orders
        ], 200);

        } catch (Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while retrieving orders',
                'error'   => $e->getMessage(), // hapus di production jika perlu
            ], 500);
        }
    }
    public function store(Request $request)
    {
        // Validasi dasar
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'email' => 'required|email|max:191',
            'city' => 'nullable|string|max:150',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'total_cost' => 'required|numeric',
            'payment_method' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'items' => 'nullable|string', // items dikirim sebagai JSON string (client mengirim JSON di FormData)
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $items = [];
        if ($request->filled('items')) {
            try {
                $items = json_decode($request->input('items'), true) ?? [];
                if (!is_array($items)) {
                    throw new \Exception('Items must be a JSON array');
                }
            } catch (\Throwable $e) {
                return response()->json([
                    'message' => 'Invalid items format: ' . $e->getMessage()
                ], 422);
            }
        }

        // 🔹 Generate nomor pesanan unik
        $datePart = now()->format('ymd'); // YYMMDD
        $randomPart = strtoupper(Str::random(8)); // huruf kapital + angka
        $noPesanan = $datePart . $randomPart;

        // Gunakan transaksi untuk atomicity
        DB::beginTransaction();
        try{
            $order = Order::create([
                'no_pesanan' => $noPesanan,
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'city' => $request->input('city'),
                'address' => $request->input('address'),
                'phone' => $request->input('phone'),
                'total_cost' => $request->input('total_cost'),
                'payment_method' => $request->input('payment_method'),
                'status' => $request->input('status', 'belum bayar'),
            ]);

             // Simpan items jika ada
            foreach ($items as $it) {
                // tiap item sebaiknya memiliki productId, name, price, quantity
                $productId = $it['productId'] ?? $it['product_id'] ?? null;
                $name = $it['name'] ?? null;
                $price = isset($it['price']) ? (float)$it['price'] : 0;
                $quantity = isset($it['quantity']) ? (int)$it['quantity'] : 0;
                $subtotal = $price * $quantity;

                // skip jika quantity 0
                if ($quantity <= 0) {
                    continue;
                }

                $order->items()->create([
                    'product_id' => $productId,
                    'name' => $name,
                    'price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Order created',
                'id' => $order->id,
            ], 201);
        } catch (\Throwable $error) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create order',
                'error' => $error->getMessage()
            ], 500);
        }
    }
}
