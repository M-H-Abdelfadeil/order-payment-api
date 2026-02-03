<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $order = Order::inRandomOrder()->first() ?? Order::factory()->create();
        $unitPrice = $this->faker->randomFloat(2, 10, 500);
        $quantity = $this->faker->numberBetween(1, 5);

        return [
            'order_id' => $order->id,
            'product_name' => $this->faker->word(),
            'product_sku' => strtoupper($this->faker->bothify('SKU-####')),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'subtotal' => $unitPrice * $quantity,
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
