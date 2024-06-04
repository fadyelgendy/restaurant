<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Models\User;
use App\Traits\OrderTrait;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    use OrderTrait;

    public function create(CreateOrderRequest $request)
    {
        $validated = $request->validated();

        $customer = User::find(2); // TODO: Logged in User

        $validated['sub_total'] = Order::calculateSubTotal($validated['products']);
        $validated['total'] = Order::calculateTotal($validated['sub_total']);

        $order = $customer->orders()->create($validated);
        $order->orderProducts()->createMany($validated['products']);

        $this->updateStock($order);

        return response()->json([
            'status' => 201,
            'data' => [
                'message' => 'Order Created Successfully'
            ]
        ]);
    }
}
