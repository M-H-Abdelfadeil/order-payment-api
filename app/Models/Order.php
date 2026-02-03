<?php

namespace App\Models;


use App\Enums\OrderStatusEnum;
use App\Enums\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatusEnum::class,
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'discount' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . date('Ymd') . '-' . str_pad($order->id ?? (Order::max('id') + 1), 5, '0', STR_PAD_LEFT);
            }
        });
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }


    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope a query to only include orders with a specific status.
     */
    public function scopeWithStatus($query, OrderStatusEnum|string $status)
    {
        $statusValue = $status instanceof OrderStatusEnum ? $status->value : $status;
        return $query->where('status', $statusValue);
    }


    public function canBePaid(): bool
    {
        return $this->status === OrderStatusEnum::CONFIRMED;
    }


    public function canBeDeleted(): bool
    {
        return $this->payments()->count() === 0;
    }


    public function hasSuccessfulPayment(): bool
    {
        return $this->payments()
            ->where('status', PaymentStatusEnum::SUCCESSFUL->value)
            ->exists();
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->total_amount = $this->subtotal + $this->tax - $this->discount;
    }
}
