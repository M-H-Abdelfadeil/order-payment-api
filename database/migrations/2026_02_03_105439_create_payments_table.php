<?php

use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
             $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('payment_number')->unique();
            $table->decimal('amount', 10, 2);
            $table->enum('payment_method', PaymentMethodEnum::values());
            $table->enum('status', PaymentStatusEnum::values())->default(PaymentStatusEnum::PENDING->value);
            $table->string('transaction_id')->nullable()->unique();
            $table->json('gateway_response')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            
            $table->index('order_id');
            $table->index('payment_method');
            $table->index('status');
            $table->index('transaction_id');
            $table->index('created_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
