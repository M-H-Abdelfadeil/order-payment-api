<?php

namespace App\Exceptions;

use App\Services\ResponseService;
use Exception;
use Illuminate\Http\JsonResponse;

class PaymentException extends Exception
{

    public static function notFound(int $paymentId): self
    {
        return new self(__("messages.Payment with ID :id not found.", ['id' => $paymentId]),  404);
    }


    public static function processingFailed(string $reason): self
    {
        return new self(__("messages.Payment processing failed: :reason", ['reason' => $reason]), 422);
    }


    public static function invalidMethod(string $method): self
    {
        return new self(__("messages.Invalid payment method: :method", ['method' => $method]), 422);
    }


    public static function gatewayError(string $message): self
    {
        return new self(__("messages.Payment gateway error: :message", ['message' => $message]), 500);
    }

    public static function insufficientAmount(): self
    {
        return new self(__("messages.Payment amount is insufficient to cover the order total."), 422);
    }


    public static function duplicatePayment(): self
    {
        return new self(__("messages.Duplicate payment for this order exists."), 422);

    }

 
    public static function unauthorized(): self
    {
        return new self(__("messages.Unauthorized access to payment."), 403);
    }


    public static function refundFailed(string $reason): self
    {
        return new self(__("messages.Payment refund failed: :reason", ['reason' => $reason]), 422);

    }

  
    public function render(): JsonResponse
    {
        return ResponseService::sendBadRequest($this->getMessage());
    }
}