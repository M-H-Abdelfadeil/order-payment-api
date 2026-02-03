<?php

namespace App\Http\Requests\API\Order;

use App\Enums\OrderStatus;
use App\Enums\OrderStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'items' => ['sometimes', 'array', 'min:1'],
            'items.*.product_name' => ['required_with:items', 'string', 'max:255'],
            'items.*.product_sku' => ['nullable', 'string', 'max:100'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1'],
            'items.*.price' => ['required_with:items', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
            'tax' => ['sometimes', 'numeric', 'min:0'],
            'discount' => ['sometimes', 'numeric', 'min:0'],
            'status' => ['sometimes', Rule::enum(OrderStatusEnum::class)],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'items.required' => __("validation.Order must have at least one item"),
            'items.array' => __("validation.Items must be an array"),
            'items.min' => __("validation.Order must have at least one item"),
            'items.*.product_name.required' => __("validation.Product name is required for each item"),
            'items.*.quantity.required' => __("validation.Quantity is required for each item"),
            'items.*.quantity.min' => __("validation.Quantity must be at least 1"),
            'items.*.price.required' => __("validation.Price is required for each item"),
            'items.*.price.min' => __("validation.Price cannot be negative"),
            'tax.min' => __("validation.Tax cannot be negative"),
            'discount.min' => __("validation.Discount cannot be negative"),
        ];
    }
}
