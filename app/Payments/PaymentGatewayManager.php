<?php

namespace App\Payments;

use App\Enums\PaymentMethodEnum;
use App\Exceptions\PaymentException;
use App\Payments\Contracts\PaymentGatewayInterface;
use App\Payments\Gateways\CreditCardGateway;
use App\Payments\Gateways\PaypalGateway;
use App\Payments\Gateways\BankTransferGateway;


class PaymentGatewayManager
{
    public function resolve($method): PaymentGatewayInterface
    {
        return match ($method) {
            PaymentMethodEnum::CREDIT_CARD->value => app(CreditCardGateway::class),
            PaymentMethodEnum::PAYPAL->value => app(PaypalGateway::class),
            PaymentMethodEnum::BANK_TRANSFER->value => app(BankTransferGateway::class),
            default => throw PaymentException::invalidMethod($method->value),
        };
    }
}
