<?php

namespace App\Payments\Contracts;

use App\Models\Order;
use App\Models\Payment;

interface PaymentGatewayInterface
{
    public function process(Order $order, Payment $payment): array;
}
