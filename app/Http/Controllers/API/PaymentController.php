<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Payment\ProcessPaymentRequest;
use App\Models\Order;
use App\Services\PaymentService;
use App\Services\ResponseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentController extends Controller
{
    public function process(ProcessPaymentRequest $request, Order $order, PaymentService $service)
    {
        $payment = $service->process($order, $request->validated());
        return ResponseService::sendResponseSuccess($payment , Response::HTTP_OK , __("messages.Payment processed successfully"));
    }


    
}
