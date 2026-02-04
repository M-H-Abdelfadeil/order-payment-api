# Order & Payment API

Laravel API for orders and payments management with extensible payment gateways.

## Setup

```
git clone https://github.com/M-H-Abdelfadeil/order-payment-api.git
cd order-payment-api
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
php artisan serve
```

API runs on http://localhost:8000

## Docs

Postman: https://documenter.getpostman.com/view/40132209/2sBXc7MQPW

Or import `order-payment-api.postman_collection.json` from project root.

## Payment Gateways

Used strategy pattern for payment methods. To add new gateway:

1. Create class in `app/Payments/Gateways/` that implements `PaymentGatewayInterface`

```php
class StripeGateway implements PaymentGatewayInterface
{
    public function process(Order $order, Payment $payment): array
    {
        return [
            'success' => true,
            'transaction_id' => 'STRIPE-' . Str::uuid(),
            'response' => [
                'provider' => 'Stripe',
                'message' => 'Payment completed'
            ],
        ];
    }
}
```

2. Add to `PaymentMethodEnum`
3. Register in `PaymentGatewayManager`

## Notes

- Supports EN/AR (set `APP_LOCALE` in .env)
