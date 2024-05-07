<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderDetail;
use App\Models\Food;
use App\Models\CustomerModel;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function orderlist()
    {
        // Ambil daftar pesanan beserta detailnya
        $orders = Order::with('orderDetails')->get();

        return response()->json([
            'status' => true,
            'data' => $orders,
            'message' => 'Order list has been retrieved'
        ]);
    }
    /**
     * Mengambil detail pesanan berdasarkan ID pesanan.
     *
     * @param int $orderId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getOrderDetails($orderId)
    {
        // Mengambil detail pesanan dari database
        $orderDetails = OrderDetail::join('food', 'order_details.food_id', '=', 'food.id_food')
            ->where('order_details.order_id', $orderId)
            ->select('food.id_food', 'food.name', 'food.price')
            ->get();

        return $orderDetails;
    }

    /**
     * Menampilkan detail pesanan dalam bentuk view.
     *
     * @param int $orderId
     * @return \Illuminate\Contracts\View\View
     */
    public function showOrderDetails($orderId)
    {
        // Ambil detail pesanan
        $orderDetails = $this->getOrderDetails($orderId);
         // Tampilkan detail pesanan dalam bentuk view
        return view('orderDetails', ['orderDetails' => $orderDetails]);
    }

    public function order(Request $request)
    {        
        // Validasi data pesanan
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required',
            'table_number' => 'required',
            'order_date' => 'required',
            'order_detail' => 'required|array',
            'order_detail.*.food_id' => 'required|integer',
            'order_detail.*.price' => 'required|integer',
            'order_detail.*.quantity' => 'required|integer'
        ]);
        // Jika validasi gagal, kirim respons dengan status 400
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }
        // Buat atau ambil data pelanggan
        $customer = CustomerModel::firstOrCreate([
            'name' => $request->customer_name
        ]);
        // Buat pesanan baru
        $order = Order::create([
            'customer_name' => $request->customer_name,
            'table_number' => $request->table_number,
            'order_date' => $request->order_date
        ]);
        // Simpan detail pesanan
        foreach ($request->order_detail as $detail) {
            OrderDetail::create([
                'order_id' => $order->id,
                'food_id' => $detail['food_id'],
                'quantity' => $detail['quantity'],
                'price' => $detail['price']
            ]);
        }
        // Kirim respons dengan status 200
        return response()->json([
            'status' => true,
            'data' => $order,
            'message' => 'Order list has been created'
        ], 201);
    }
}
