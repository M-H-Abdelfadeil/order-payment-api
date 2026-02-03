<?php

namespace App\Enums;

use App\Contracts\AppEnum;
use App\Traits\EnumHelpers;

enum OrderStatusEnum: string implements AppEnum
{
    use EnumHelpers;

    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';

 

    public function label(): string
    {
        return match($this) {
            self::PENDING => __('order.Pending'),
            self::CONFIRMED =>  __('order.Confirmed'),
            self::CANCELLED =>  __('order.Cancelled'),
        };
    }


    public function canBePaid(): bool
    {
        return $this === self::CONFIRMED;
    }

     public function canBeDeleted(): bool
    {
        return true; 
    }
}
