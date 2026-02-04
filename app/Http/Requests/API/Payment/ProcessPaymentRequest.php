<?php

namespace App\Http\Requests\API\Payment;

use App\Enums\PaymentMethod;
use App\Enums\PaymentMethodEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProcessPaymentRequest extends FormRequest
{
  


    public function rules(): array
    {
        return [
            'payment_method' => ['required', Rule::enum(PaymentMethodEnum::class)],
            'amount' => ['sometimes', 'numeric', 'min:0.01'],
        ];
    }

 
    public function messages(): array
    {
        return [
            'payment_method.required' => 'Payment method is required',
            'payment_method.enum' => 'Invalid payment method',
            'amount.numeric' => 'Amount must be a valid number',
            'amount.min' => 'Amount must be greater than 0',
        ];
    }
}