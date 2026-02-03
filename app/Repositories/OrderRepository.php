<?php

namespace App\Repositories;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;


class OrderRepository implements OrderRepositoryInterface
{

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Order::query()->with(['user', 'items', 'payments']);

        $this->applyFilters($query, $filters);

        $query->latest();

        return $query->paginate($perPage);
    }


    public function find(int $id): ?Order
    {
        return Order::with(['user', 'items', 'payments'])->find($id);
    }

  
    public function findOrFail(int $id): Order
    {
        return Order::with(['user', 'items', 'payments'])->findOrFail($id);
    }

 
    public function findByOrderNumber(string $orderNumber): ?Order
    {
        return Order::with(['user', 'items', 'payments'])
            ->where('order_number', $orderNumber)
            ->first();
    }

    public function getByUser(int $userId, array $filters = []): Collection
    {
        $query = Order::with(['items', 'payments'])
            ->where('user_id', $userId);

        $this->applyFilters($query, $filters);

        return $query->latest()->get();
    }


    public function create(array $data): Order
    {
        $order = Order::create([
            'user_id' => $data['user_id'],
            'subtotal' => $data['subtotal'] ?? 0,
            'tax' => $data['tax'] ?? 0,
            'discount' => $data['discount'] ?? 0,
            'total_amount' => $data['total_amount'],
            'status' => $data['status'] ?? OrderStatusEnum::PENDING->value,
            'notes' => $data['notes'] ?? null,
        ]);

        // Create  items 
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                $order->items()->create([
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['product_sku'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['quantity'] * $item['price'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            $order->calculateTotals();
            $order->save();
        }

        return $order->fresh(['items', 'payments']);
    }

  
    public function update(Order $order, array $data): Order
    {
        $order->update(array_filter([
            'status' => $data['status'] ?? $order->status,
            'tax' => $data['tax'] ?? $order->tax,
            'discount' => $data['discount'] ?? $order->discount,
            'notes' => $data['notes'] ?? $order->notes,
        ]));

        if (isset($data['items']) && is_array($data['items'])) {
            // Delete old items
            $order->items()->delete();

            // Create new items
            foreach ($data['items'] as $item) {
                $order->items()->create([
                    'product_name' => $item['product_name'],
                    'product_sku' => $item['product_sku'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'subtotal' => $item['quantity'] * $item['price'],
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            $order->calculateTotals();
            $order->save();
        }

        return $order->fresh(['items', 'payments']);
    }

    public function delete(Order $order): bool
    {
        return $order->delete();
    }

    public function getByStatus(string $status): Collection
    {
        return Order::with(['user', 'items', 'payments'])
            ->withStatus($status)
            ->latest()
            ->get();
    }

    public function countByStatus(string $status): int
    {
        return Order::withStatus($status)->count();
    }

 
    public function hasPayments(Order $order): bool
    {
        return $order->payments()->exists();
    }

    private function applyFilters($query, array $filters): void
    {
        if (isset($filters['status'])) {
            $query->withStatus($filters['status']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['from_date'])) {
            $query->whereDate('created_at', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('created_at', '<=', $filters['to_date']);
        }

        if (isset($filters['min_amount'])) {
            $query->where('total_amount', '>=', $filters['min_amount']);
        }

        if (isset($filters['max_amount'])) {
            $query->where('total_amount', '<=', $filters['max_amount']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
    }
}