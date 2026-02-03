<?php

namespace App\Exceptions;

use App\Services\ResponseService;
use Exception;
use Illuminate\Http\JsonResponse;

class OrderException extends Exception
{
    
    public static function notFound(int $orderId): self
    {
        return new self(__("messages.Order with ID :id not found.", ['id' => $orderId]), 404);
    }

  
    public static function cannotBeDeleted(string $reason = 'Order has associated payments'): self
    {
        return new self(__("messages.Order cannot be deleted. Reason: :reason", ['reason' => $reason]), 422);
    }

    
    public static function cannotBePaid(string $status): self
    {
        return new self(__("messages.Order with status :status cannot be paid. Only confirmed orders can be paid.", ['status' => $status]), 422);
    }


    public static function unauthorized(): self
    {
        return new self(__("messages.You are not authorized to access this order."), 403);
    }


    public static function invalidStatusTransition(string $from, string $to): self
    {
        return new self(__("messages.Cannot change order status from :from to :to.", ['from' => $from, 'to' => $to]), 422);
    }

    public function render(): JsonResponse
    {
        return ResponseService::sendBadRequest($this->getMessage());
    }
}