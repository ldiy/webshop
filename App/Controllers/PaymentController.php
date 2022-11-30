<?php

namespace App\Controllers;

use App\Models\Order;
use Core\Exceptions\HttpNotFoundException;
use Core\Http\Request;
use Core\Http\Response;

class PaymentController
{
    public function show(Request $request, int $orderId): Response
    {
        $order = Order::find($orderId);
        if (is_null($order)) {
            throw new HttpNotFoundException('Order not found.');
        }

        // Check if the order belongs to the current user
        if ($order->user_id !== auth()->user()->id) {
            throw new HttpNotFoundException('Order not found.');
        }

        // Check if the order is already paid
        if ($order->paid_at !== null) {
            return redirect('/order/' . $order->id);
        }

        return view('pay', [
            'order' => $order,
        ]);
    }

    public function store(Request $request, int $orderId): Response
    {
        $order = Order::find($orderId);
        if (is_null($order)) {
            throw new HttpNotFoundException('Order not found.');
        }

        // Check if the order belongs to the current user
        if ($order->user_id !== auth()->user()->id) {
            throw new HttpNotFoundException('Order not found.');
        }

        // Check if the order is already paid
        if ($order->paid_at !== null) {
            return redirect('/order/' . $order->id);
        }

        // Mark the order as paid
        $order->paid_at = date('Y-m-d H:i:s');
        $order->status = Order::findStatusCode('paid');
        $order->save();

        return redirect('/order/' . $order->id);
    }
}