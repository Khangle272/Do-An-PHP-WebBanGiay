<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable()->after('product_id')
                ->constrained('product_variants')->nullOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable()->after('product_id')
                ->constrained('product_variants')->nullOnDelete();
        });

        // Lưu ý: KHÔNG xóa product_size_id/product_color_id (cart_items) hoặc
        // product_size_id/product_color_id (order_items) trong migration này.
        // Sau khi đã chuyển toàn bộ code + dữ liệu sang dùng product_variant_id
        // và xác nhận ổn định, tạo thêm 1 migration riêng để drop các cột cũ đó.
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_variant_id');
        });
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_variant_id');
        });
    }
};
