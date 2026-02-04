<?php

namespace App\Payments\Gateways;

use App\Models\Order;
use App\Models\Payment;
use App\Payments\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Str;

class PaypalGateway implements PaymentGatewayInterface
{
    public function process(Order $order, Payment $payment): array
    {
        return [
            'success' => true,
            'transaction_id' => 'PP-' . Str::uuid(),
            'response' => [
                'provider' => 'Paypal',
                'message' => 'Paypal payment successful'
            ],
        ];
    }
}
