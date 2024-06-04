<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Models\User;
use App\Traits\OrderTrait;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    use OrderTrait, ResponseTrait;

    public function create(CreateOrderRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            if (!$this->isStockAvailableFor($validated['products'])) {
                dd($this->isStockAvailableFor($validated['products']));
                return $this->failResponseJson(trans('Product and/or it\'s ingredient(s) is Out Of Stock!'));
            }

            $customer = Auth::user();

            $validated['sub_total'] = Order::calculateSubTotal($validated['products']);
            $validated['total'] = Order::calculateTotal($validated['sub_total']);

            $order = $customer->orders()->create($validated);
            $order->orderProducts()->createMany($validated['products']);

            $this->updateStock($order);

            DB::commit();

            return $this->successResponseJson(trans('Order Created Successfully!'), 201);
        } catch (Exception $exception) {
            DB::rollBack();
            return $this->failResponseJson($exception->getMessage());
        }
    }
}
