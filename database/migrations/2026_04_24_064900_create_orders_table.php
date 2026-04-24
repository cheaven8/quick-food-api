<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_number')->unique();

            // relations
            $table->foreignId('customer_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('hotel_id')
                ->constrained('hotel_profiles')
                ->onDelete('cascade');

            // order status
            $table->string('status')->default('pending');

            // pricing
            $table->decimal('subtotal', 10, 2);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('total', 10, 2);

            // delivery info
            $table->text('delivery_address');
            $table->string('customer_phone');
            $table->text('special_instructions')->nullable();

            $table->timestamp('delivered_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};