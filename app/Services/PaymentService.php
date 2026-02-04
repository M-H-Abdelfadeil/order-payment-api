<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Exceptions\PaymentException;
use App\Models\Order;
use App\Models\Payment;
use App\Payments\PaymentGatewayManager;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(
        private PaymentRepositoryInterface $payments,
        private PaymentGatewayManager $gatewayManager
    ) {}

    public function process(Order $order, array $data): Payment
    {
    
        if ($order->status !== OrderStatusEnum::CONFIRMED->value) {
            Log::warning([
                'message' => 'Attempted payment on unconfirmed order',
                'order_id' => $order->id,
                'order_status' => $order->status,
            ]);
            throw PaymentException::processingFailed(__('messages.Order must be confirmed before payment.'));
        }

        if ($this->payments->orderHasSuccessfulPayment($order)) {
            Log::warning([
                'message' => 'Duplicate payment attempt detected',
                'order_id' => $order->id,
            ]);
            throw PaymentException::duplicatePayment();
        }

        $payment = $this->payments->create([
            'order_id' => $order->id,
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
        ]);

        $gateway = $this->gatewayManager->resolve($payment->payment_method);

        $result = $gateway->process($order, $payment);

        if ($result['success']) {
            $payment->markAsSuccessful(
                $result['transaction_id'] ?? null,
                $result['response']
            );
        } else {
            Log::warning([
                'message' => 'Payment gateway processing failed',
                'order_id' => $order->id,
                'payment_id' => $payment->id,
                'gateway_response' => $result,
            ]);
            $payment->markAsFailed('Gateway processing failed', $result);
        }

        return $payment;
    }
}
