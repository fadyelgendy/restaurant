<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
     /**
     * Handle the Order "created" event.
     */
    public function creating(Order $order): void
    {
        $order->order_number = 'ORDER#'. time();
        $order->tax = Order::TAX;
    }

    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // TODO: Notigy customer & merchant Via Email
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // TODO: Notify Customer
        // TODO: Notify Merchant if for example Order Cancelled
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
