<?php

namespace App\Payments\Gateways;

use App\Models\Order;
use App\Models\Payment;
use App\Payments\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Str;

class CreditCardGateway implements PaymentGatewayInterface
{
    public function process(Order $order, Payment $payment): array
    {
        return [
            'success' => true,
            'transaction_id' => 'CC-' . Str::uuid(),
            'response' => [
                'provider' => 'CreditCard',
                'message' => 'Payment processed successfully'
            ],
        ];
    }
}
