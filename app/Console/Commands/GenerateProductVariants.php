<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateProductVariants extends Command
{
    protected $signature = 'products:generate-variants
        {--product= : Chỉ chạy cho 1 product_id cụ thể (bỏ trống = chạy hết)}
        {--force : Xóa variants cũ của sản phẩm rồi sinh lại (mặc định: bỏ qua sản phẩm đã có variants)}';

    protected $description = 'Tự sinh product_variants (size x màu) từ dữ liệu product_sizes/product_colors cũ';

    public function handle(): int
    {
        $query = Product::with(['sizes', 'colors', 'variants']);

        if ($productId = $this->option('product')) {
            $query->where('id', $productId);
        }

        $products = $query->get();
        $created = 0;
        $skipped = 0;

        foreach ($products as $product) {
            if ($product->variants->count() > 0 && !$this->option('force')) {
                $this->line("- [{$product->id}] {$product->name}: đã có variants, bỏ qua (dùng --force nếu muốn sinh lại)");
                $skipped++;
                continue;
            }

            $sizes = $product->sizes;
            $colors = $product->colors;

            if ($sizes->isEmpty() && $colors->isEmpty()) {
                $this->line("- [{$product->id}] {$product->name}: không có size/màu cũ, bỏ qua");
                $skipped++;
                continue;
            }

            DB::transaction(function () use ($product, $sizes, $colors) {
                if ($this->option('force')) {
                    $product->variants()->delete();
                }

                if ($sizes->isNotEmpty() && $colors->isNotEmpty()) {
                    // Sinh tổ hợp chéo: mỗi size x mỗi màu
                    foreach ($sizes as $size) {
                        foreach ($colors as $color) {
                            ProductVariant::create([
                                'product_id' => $product->id,
                                'size' => $size->size,
                                'color_name' => $color->color_name,
                                'color_code' => $color->color_code,
                                // Lấy số nhỏ hơn giữa 2 bên cho an toàn, tránh khai khống tồn kho
                                'stock' => min($size->stock, $color->stock),
                            ]);
                        }
                    }
                } elseif ($sizes->isNotEmpty()) {
                    foreach ($sizes as $size) {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'size' => $size->size,
                            'color_name' => null,
                            'color_code' => null,
                            'stock' => $size->stock,
                        ]);
                    }
                } else {
                    foreach ($colors as $color) {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'size' => null,
                            'color_name' => $color->color_name,
                            'color_code' => $color->color_code,
                            'stock' => $color->stock,
                        ]);
                    }
                }
            });

            $this->info("- [{$product->id}] {$product->name}: đã sinh variants");
            $created++;
        }

        $this->newLine();
        $this->info("Xong! Đã sinh cho {$created} sản phẩm, bỏ qua {$skipped} sản phẩm.");
        $this->warn('Lưu ý: số lượng tồn kho ở đây là ƯỚC LƯỢNG (min giữa size & màu), không phải số thật theo từng tổ hợp. Vào Admin kiểm tra và chỉnh lại cho đúng thực tế trước khi bán.');

        return self::SUCCESS;
    }
}