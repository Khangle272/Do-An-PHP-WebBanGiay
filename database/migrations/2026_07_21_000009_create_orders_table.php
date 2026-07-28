<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('order_code')->unique();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone', 20);
            $table->text('address');
            $table->text('note')->nullable();
            $table->decimal('shipping_fee', 12, 0)->default(0);
            $table->decimal('total_price', 12, 0);
            $table->string('status')->default('pending');
            $table->string('payment_method')->default('cod');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
