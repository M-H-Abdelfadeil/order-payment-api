<?php

namespace App\Enums;

use App\Contracts\AppEnum;
use App\Traits\EnumHelpers;

enum PaymentStatusEnum: string implements AppEnum
{
    use EnumHelpers;
    
    case PENDING = 'pending';
    case SUCCESSFUL = 'successful';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';


    public function label(): string
    {
        return match($this) {
            self::PENDING => __('payment.Pending'),
            self::SUCCESSFUL => __('payment.Successful'),
            self::FAILED => __('payment.Failed'),
            self::REFUNDED => __('payment.Refunded'),
        };
    }


     public function isFinal(): bool
    {
        return in_array($this, [self::SUCCESSFUL, self::REFUNDED]);
    }
    
    
    public function canBeRefunded(): bool
    {
        return $this === self::SUCCESSFUL;
    }
}
