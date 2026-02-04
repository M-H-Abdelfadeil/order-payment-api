<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Enums\OrderStatusEnumEnum;
use App\Exceptions\OrderException;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class OrderService
{
   
    public function __construct(
        private OrderRepositoryInterface $orderRepository
    ) {}


    public function getAllOrders(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->paginate($filters, $perPage);
    }

   
    public function getUserOrders(int $userId, array $filters = [] , int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->getByUser($userId, $filters);
    }

    public function findOrder(int $id): Order
    {
        $order = $this->orderRepository->find($id);

        if (!$order) {
            throw OrderException::notFound($id);
        }

        return $order;
    }

   
    public function findByOrderNumber(string $orderNumber): Order
    {
        $order = $this->orderRepository->findByOrderNumber($orderNumber);

        if (!$order) {
            throw new OrderException("Order {$orderNumber} not found", 404);
        }

        return $order;
    }


     public function findByUser(int $userId, int $order_id): Order
    {
        $order = $this->orderRepository->findByUser($userId , $order_id);

        if (!$order) {
            throw new OrderException("Order {$order_id} not found", 404);
        }

        return $order;
    }

   
    public function createOrder(array $data): Order
    {
        try {
            DB::beginTransaction();
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $subtotal += $item['quantity'] * $item['price'];
            }

            $tax = $data['tax'] ?? 0;
            $discount = $data['discount'] ?? 0;
            $total = $subtotal + $tax - $discount;

            $order = $this->orderRepository->create([
                'user_id' => $data['user_id'],
                'items' => $data['items'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total_amount' => $total,
                'status' => $data['status'] ?? OrderStatusEnum::PENDING->value,
                'notes' => $data['notes'] ?? null,
            ]);

            DB::commit();

            Log::info('Order created successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => $order->user_id,
            ]);

            return $order;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create order', [
                'error' => $e->getMessage(),
                'user_id' => $data['user_id'] ?? null,
            ]);
            throw $e;
        }
    }


    public function updateOrder(Order $order, array $data): Order
    {
        try {
            DB::beginTransaction();

            // Check if order can be updated
            if ($order->hasSuccessfulPayment()) {
                throw OrderException::invalidStatusTransition(
                    $order->status->value,
                    'updated (order has successful payment)'
                );
            }

            $order = $this->orderRepository->update($order, $data);

            DB::commit();

            Log::info('Order updated successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);

            return $order;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update order', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }


    public function deleteOrder(Order $order): bool
    {
        if (!$order->canBeDeleted()) {
            throw OrderException::cannotBeDeleted();
        }

        try {
            DB::beginTransaction();

            $result = $this->orderRepository->delete($order);

            DB::commit();

            Log::info('Order deleted successfully', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);

            return $result;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete order', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }


    public function updateOrderStatusEnum(Order $order, OrderStatusEnum $newStatus): Order
    {
        $oldStatus = $order->status;

     
        if (!$this->canTransitionStatus($oldStatus, $newStatus)) {
            throw OrderException::invalidStatusTransition(
                $oldStatus->value,
                $newStatus->value
            );
        }

        $order = $this->orderRepository->update($order, [
            'status' => $newStatus->value,
        ]);

        Log::info('Order status updated', [
            'order_id' => $order->id,
            'old_status' => $oldStatus->value,
            'new_status' => $newStatus->value,
        ]);

        return $order;
    }


    public function getOrdersByStatus(OrderStatusEnum $status): Collection
    {
        return $this->orderRepository->getByStatus($status->value);
    }


    public function getOrderStatistics(): array
    {
        return [
            'total' => Order::count(),
            'pending' => $this->orderRepository->countByStatus(OrderStatusEnum::PENDING->value),
            'confirmed' => $this->orderRepository->countByStatus(OrderStatusEnum::CONFIRMED->value),
            'cancelled' => $this->orderRepository->countByStatus(OrderStatusEnum::CANCELLED->value),
            'total_revenue' => Order::where('status', OrderStatusEnum::CONFIRMED->value)
                ->sum('total_amount'),
        ];
    }

  
    private function canTransitionStatus(OrderStatusEnum $from, OrderStatusEnum $to): bool
    {
        // Define allowed transitions
        $allowedTransitions = [
            OrderStatusEnum::PENDING->value => [
                OrderStatusEnum::CONFIRMED->value,
                OrderStatusEnum::CANCELLED->value,
            ],
            OrderStatusEnum::CONFIRMED->value => [
                OrderStatusEnum::CANCELLED->value,
            ],
            OrderStatusEnum::CANCELLED->value => [], 
        ];

        return in_array(
            $to->value,
            $allowedTransitions[$from->value] ?? []
        );
    }

   
    public function verifyOwnership(Order $order, int $userId): void
    {
        if ($order->user_id !== $userId) {
            throw OrderException::unauthorized();
        }
    }
}