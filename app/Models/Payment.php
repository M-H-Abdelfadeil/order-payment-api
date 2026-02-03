<?php

namespace App\Models;

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payment_method' => PaymentMethodEnum::class,
            'status' => PaymentStatusEnum::class,
            'amount' => 'decimal:2',
            'gateway_response' => 'array',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_number)) {
                $nextId = $payment->id ?? (Payment::max('id') + 1);
                $payment->payment_number = 'PAY-' . date('Ymd') . '-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeWithStatus($query, PaymentStatusEnum|string $status)
    {
        $statusValue = $status instanceof PaymentStatusEnum ? $status->value : $status;
        return $query->where('status', $statusValue);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', PaymentStatusEnum::SUCCESSFUL->value);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', PaymentStatusEnum::FAILED->value);
    }


    public function markAsSuccessful(string $transactionId, array $gatewayResponse = []): void
    {
        $this->update([
            'status' => PaymentStatusEnum::SUCCESSFUL,
            'transaction_id' => $transactionId,
            'gateway_response' => $gatewayResponse,
            'processed_at' => now(),
            'error_message' => null,
        ]);
    }

    /**
     * Mark payment as failed.
     */
    public function markAsFailed(string $errorMessage, array $gatewayResponse = []): void
    {
        $this->update([
            'status' => PaymentStatusEnum::FAILED,
            'gateway_response' => $gatewayResponse,
            'error_message' => $errorMessage,
            'processed_at' => now(),
        ]);
    }


    public function canBeRefunded(): bool
    {
        return $this->status->canBeRefunded();
    }

    /**
     * Check if payment is final.
     */
    public function isFinal(): bool
    {
        return $this->status->isFinal();
    }
}
