<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CartItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    public function index(){
        $orders = Order::with('customer', 'items.product', 'cartItems')->get(); // to add ti the model cart items relationship
        $orderData = [];

        foreach ($orders as $order) {
            $customer = $order->customer;
            $items = $order->items;

            $totalAmount = $items->sum(function ($item) {
                return $item->price * $item->quantity;
            });
            $itemsCount = $items->count();
            $lastAddedToCart = $order->cartItems->max('created_at') ?? null;
            $completedOrderExists = $order->status === 'completed' ? true : false;   
            $orderData[] = [
                'order_id' => $order->id,
                'customer_name' => $customer->name,
                'total_amount' => $totalAmount,
                'items_count' => $itemsCount,
                'last_added_to_cart' => $lastAddedToCart,
                'completed_order_exists' => $completedOrderExists,
                'created_at' => $order->created_at
            ];
        }
        usort($orderData, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return view('orders.index', ['orders' => $orderData]);

    }
    public function indexOld()
    {
        $orders = Order::all();
        $orderData = [];

        foreach ($orders as $order) {
            $customer = $order->customer;
            $items = $order->items;
            $totalAmount = 0;
            $itemsCount = 0;

            foreach ($items as $item) {
                $product = $item->product;
                $totalAmount += $item->price * $item->quantity;
                $itemsCount++;
            }

            $lastAddedToCart = CartItem::where('order_id', $order->id)
                ->orderByDesc('created_at')
                ->first()
                ->created_at ?? null;

            $completedOrderExists = Order::where('id', $order->id)
                ->where('status', 'completed')
                ->exists();

            $orderData[] = [
                'order_id' => $order->id,
                'customer_name' => $customer->name,
                'total_amount' => $totalAmount,
                'items_count' => $itemsCount,
                'last_added_to_cart' => $lastAddedToCart,
                'completed_order_exists' => $completedOrderExists,
                'created_at' => $order->created_at,
            ];
        }

        usort($orderData, function($a, $b) {
            $aCompletedAt = Order::where('id', $a['order_id'])
                ->where('status', 'completed')
                ->orderByDesc('completed_at')
                ->first()
                ->completed_at ?? null;

            $bCompletedAt = Order::where('id', $b['order_id'])
                ->where('status', 'completed')
                ->orderByDesc('completed_at')
                ->first()
                ->completed_at ?? null;

            return strtotime($bCompletedAt) - strtotime($aCompletedAt);
        });

        return view('orders.index', ['orders' => $orderData]);
    }
}