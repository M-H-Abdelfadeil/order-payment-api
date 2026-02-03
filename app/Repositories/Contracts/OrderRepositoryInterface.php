<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;


interface OrderRepositoryInterface
{

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?Order;

    public function findOrFail(int $id): Order;

    public function findByOrderNumber(string $orderNumber): ?Order;

    public function getByUser(int $userId, array $filters = []): Collection;

    public function create(array $data): Order;

    public function update(Order $order, array $data): Order;

    public function delete(Order $order): bool;

    public function getByStatus(string $status): Collection;

    public function countByStatus(string $status): int;

    public function hasPayments(Order $order): bool;
}