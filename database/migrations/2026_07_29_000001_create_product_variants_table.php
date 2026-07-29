<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('size')->nullable();       // vd: "39"
            $table->string('color_name')->nullable(); // vd: "Đen"
            $table->string('color_code')->nullable(); // vd: "#000000"
            $table->unsignedInteger('stock')->default(0);
            $table->timestamps();

            // Không cho phép 2 dòng trùng nhau (cùng product + cùng size + cùng màu)
            $table->unique(['product_id', 'size', 'color_name'], 'product_variant_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
