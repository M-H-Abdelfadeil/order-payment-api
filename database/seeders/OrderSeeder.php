<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        Order::factory(10)->create()->each(function ($order) {
            
            OrderItem::factory(rand(2, 5))->create([
                'order_id' => $order->id
            ]);


            if (rand(1, 100) <= 70) {
                Payment::factory(rand(1, 2))->create([
                    'order_id' => $order->id
                ]);
            }
        });
    }
}
