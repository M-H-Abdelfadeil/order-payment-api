<?php

namespace Database\Factories;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $order = Order::inRandomOrder()->first() ?? Order::factory()->create();

        return [
            'order_id' => $order->id,
            'amount' => $this->faker->randomFloat(2, 50, 1000),
            'payment_method' => $this->faker->randomElement(PaymentMethodEnum::values()),
            'status' => $this->faker->randomElement(PaymentStatusEnum::values()),
            'transaction_id' => 'TXN-' . strtoupper(Str::random(12)),
            'gateway_response' => json_encode(['status' => 'success']),
            'error_message' => null,
            'processed_at' => now(),
        ];
    }
}
