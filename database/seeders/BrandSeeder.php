<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Nike', 'description' => 'Thương hiệu thể thao hàng đầu thế giới từ Mỹ.'],
            ['name' => 'Adidas', 'description' => 'Thương hiệu thể thao Đức nổi tiếng với công nghệ Boost.'],
            ['name' => 'Puma', 'description' => 'Thương hiệu thể thao Đức, phong cách thể thao đường phố.'],
            ['name' => 'Vans', 'description' => 'Giày trượt ván biểu tượng với phong cách cá tính.'],
            ['name' => 'Converse', 'description' => 'Huyền thoại giày canvas Chuck Taylor All Star.'],
        ];

        foreach ($brands as $brand) {
            Brand::create([
                'name' => $brand['name'],
                'slug' => Str::slug($brand['name']),
                'logo' => null,
                'status' => true,
            ]);
        }
    }
}
