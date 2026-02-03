<?php

namespace App\Enums;

use App\Contracts\AppEnum;
use App\Traits\EnumHelpers;

enum PaymentMethodEnum: string implements AppEnum
{
    use EnumHelpers;
    
    case CREDIT_CARD = 'credit_card';
    case PAYPAL = 'paypal';
    case BANK_TRANSFER = 'bank_transfer';

    /**
     * Get all available methods as array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get label for display
     */
    public function label(): string
    {
        return match($this) {
            self::CREDIT_CARD => __('payment.Credit Card'),
            self::PAYPAL => __('payment.PayPal'),
            self::BANK_TRANSFER => __('payment.Bank Transfer'),
        };
    }

   
}