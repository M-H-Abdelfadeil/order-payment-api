<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Order\StoreOrderRequest;
use App\Http\Requests\API\Order\UpdateOrderRequest;

use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use App\Services\ResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

 
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'status',
                'from_date',
                'to_date',
                'min_amount',
                'max_amount',
                'search',
            ]);

            $perPage = $request->input('per_page', 15);

            $orders = $this->orderService->getAllOrders($filters, $perPage);

            return ResponseService::sendResponseSuccess(OrderResource::collection($orders)->response()->getData());

        } catch (\Exception $e) {
            return ResponseService::sendBadRequest($e->getMessage());
        }
    }

  
    public function store(StoreOrderRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['user_id'] = auth()->id();
            $order = $this->orderService->createOrder($data);
            return ResponseService::sendResponseSuccess(new OrderResource($order), Response::HTTP_CREATED , __("messages.Order created successfully"));
        } catch (\Exception $e) {
            return ResponseService::sendBadRequest($e->getMessage());
        }
    }

 
    public function show(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->findOrder($id);

            $this->orderService->verifyOwnership($order, auth()->id());

           return ResponseService::sendResponseSuccess(new OrderResource($order));

        } catch (\Exception $e) {
            return ResponseService::sendBadRequest($e->getMessage());
        }
    }

    public function update(UpdateOrderRequest $request, int $id): JsonResponse
    {
        try {
            $order = $this->orderService->findOrder($id);

            $this->orderService->verifyOwnership($order, auth()->id());

            $order = $this->orderService->updateOrder($order, $request->validated());

            return ResponseService::sendResponseSuccess(new OrderResource($order), Response::HTTP_OK, __("messages.Order updated successfully"));
        } catch (\Exception $e) {
            return ResponseService::sendBadRequest($e->getMessage());
        }
    }


    public function destroy(int $id): JsonResponse
    {
        try {
            $order = $this->orderService->findOrder($id);

            $this->orderService->verifyOwnership($order, auth()->id());

            $this->orderService->deleteOrder($order);

            return ResponseService::sendResponseSuccess(null, Response::HTTP_OK, __("messages.Order deleted successfully"));

        } catch (\Exception $e) {
            return ResponseService::sendBadRequest($e->getMessage());
        }
    }
}