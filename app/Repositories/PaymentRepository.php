<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Models\Order;
use App\Enums\PaymentStatusEnum;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository implements PaymentRepositoryInterface
{
   

  
    public function create(array $data): Payment
    {
        return Payment::create([
            'order_id' => $data['order_id'],
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'status' => $data['status'] ?? PaymentStatusEnum::PENDING->value,
            'transaction_id' => $data['transaction_id'] ?? null,
            'gateway_response' => $data['gateway_response'] ?? null,
            'error_message' => $data['error_message'] ?? null,
            'processed_at' => $data['processed_at'] ?? null,
        ]);
    }





    public function orderHasSuccessfulPayment(Order $order): bool
    {
        return $order->payments()
            ->where('status', PaymentStatusEnum::SUCCESSFUL->value)
            ->exists();
    }

 

}