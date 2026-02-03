<?php

namespace Database\Factories;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = $this->faker->randomFloat(2, 50, 1000);
        $tax = $this->faker->randomFloat(2, 0, 0.15) * $subtotal; 
        $discount = $this->faker->randomFloat(2, 0, 0.2) * $subtotal; 
        $totalAmount = $subtotal + $tax - $discount;

        return [
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory()->create()->id,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total_amount' => $totalAmount,
            'status' => $this->faker->randomElement(OrderStatusEnum::values()),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
