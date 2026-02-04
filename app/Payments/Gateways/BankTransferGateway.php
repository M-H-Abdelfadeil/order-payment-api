<?php

namespace App\Payments\Gateways;

use App\Models\Order;
use App\Models\Payment;
use App\Payments\Contracts\PaymentGatewayInterface;

class BankTransferGateway implements PaymentGatewayInterface
{
    public function process(Order $order, Payment $payment): array
    {
        return [
            'success' => true,
            'transaction_id' => null,
            'response' => [
                'provider' => 'BankTransfer',
                'message' => 'Awaiting bank confirmation'
            ],
        ];
    }
}
