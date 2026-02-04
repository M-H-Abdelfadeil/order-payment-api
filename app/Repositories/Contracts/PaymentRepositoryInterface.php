<?php

namespace App\Repositories\Contracts;

use App\Models\Payment;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;


interface PaymentRepositoryInterface
{

    // public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    // public function find(int $id): ?Payment;

    // public function findOrFail(int $id): Payment;

    // public function findByTransactionId(string $transactionId): ?Payment;

    // public function getByOrder(Order $order): Collection;

    public function create(array $data): Payment;
    
    // public function update(Payment $payment, array $data): Payment;
    
    // public function delete(Payment $payment): bool;
    
    // public function getByStatus(string $status): Collection;
    
    public function orderHasSuccessfulPayment(Order $order): bool;
}