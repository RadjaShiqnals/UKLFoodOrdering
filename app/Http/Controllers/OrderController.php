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
        $orders = Order::with('orderDetails')->get();

        return response()->json([
            'status' => true,
            'data' => $orders,
            'message' => 'Order list has been retrieved'
        ]);
    }
    public function getOrderDetails($orderId)
    {
        $orderDetails = OrderDetail::join('food', 'order_details.food_id', '=', 'food.id_food')
            ->where('order_details.order_id', $orderId)
            ->select('food.id_food', 'food.name', 'food.price')
            ->get();

        return $orderDetails;
    }

    public function showOrderDetails($orderId)
    {
        $orderDetails = $this->getOrderDetails($orderId);

        return view('orderDetails', ['orderDetails' => $orderDetails]);
    }

    public function order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required',
            'table_number' => 'required',
            'order_date' => 'required',
            'order_detail' => 'required|array',
            'order_detail.*.food_id' => 'required|integer',
            'order_detail.*.price' => 'required|integer',
            'order_detail.*.quantity' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $customer = CustomerModel::firstOrCreate([
            'name' => $request->customer_name
        ]);

        $order = Order::create([
            'customer_name' => $request->customer_name,
            'table_number' => $request->table_number,
            'order_date' => $request->order_date
        ]);

        foreach ($request->order_detail as $detail) {
            OrderDetail::create([
                'order_id' => $order->id,
                'food_id' => $detail['food_id'],
                'quantity' => $detail['quantity'],
                'price' => $detail['price']
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $order,
            'message' => 'Order list has been created'
        ], 201);
    }
}
