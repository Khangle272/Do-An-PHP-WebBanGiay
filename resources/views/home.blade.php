@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
    <!-- Hero -->
    <section class="hero">
        <div class="container">
            <h1>Bước Chạy Đẳng Cấp</h1>
            <p>Khám phá bộ sưu tập giày thể thao mới nhất từ các thương hiệu hàng đầu thế giới. Giá tốt nhất chỉ có tại
                SneakerShop!</p>
            <a href="/san-pham" class="btn">Mua sắm ngay →</a>
        </div>
    </section>

    <div class="container">
        <!-- Danh mục -->
        <section class="mb-40">
            <div class="section-title">
                <h2>Danh mục sản phẩm</h2>
                <a href="/san-pham">Xem tất cả →</a>
            </div>
            <div class="cat-grid">
                @foreach($categories as $cat)
                    <a href="/danh-muc/{{ $cat->slug }}" class="cat-card">
                        <div class="icon">👟</div>
                        <h3>{{ $cat->name }}</h3>
                        <p>{{ $cat->products()->active()->count() }} sản phẩm</p>
                    </a>
                @endforeach
            </div>
        </section>

        <!-- Sản phẩm nổi bật -->
        <section class="mb-40">
            <div class="section-title">
                <h2>Sản phẩm nổi bật</h2>
                <a href="/san-pham?featured=1">Xem tất cả →</a>
            </div>
            <div class="product-grid">
                @foreach($featuredProducts as $product)
                    <div class="product-card">
                        @if($product->sale_price)
                            <span class="badge">-{{ $product->sale_percentage }}%</span>
                        @endif
                        <span class="badge featured" style="left: auto; right: 12px;">Nổi bật</span>
                        <a href="/san-pham/{{ $product->slug }}">
                            <div class="image-wrap">
                                <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" loading="lazy">
                            </div>
                        </a>
                        <div class="info">
                            <div class="brand-name">{{ $product->brand->name ?? '' }}</div>
                            <div class="product-name"><a href="/san-pham/{{ $product->slug }}">{{ $product->name }}</a></div>
                            <div class="price-wrap">
                                <span class="current-price">{{ number_format($product->final_price) }}₫</span>
                                @if($product->sale_price)
                                    <span class="old-price">{{ number_format($product->price) }}₫</span>
                                @endif
                            </div>
                            @if($product->avg_rating > 0)
                                <div class="rating">⭐ {{ $product->avg_rating }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Sản phẩm mới -->
        <section class="mb-40">
            <div class="section-title">
                <h2>Sản phẩm mới nhất</h2>
                <a href="/san-pham">Xem tất cả →</a>
            </div>
            <div class="product-grid">
                @foreach($newProducts as $product)
                    <div class="product-card">
                        @if($product->sale_price)
                            <span class="badge">-{{ $product->sale_percentage }}%</span>
                        @endif
                        <a href="/san-pham/{{ $product->slug }}">
                            <div class="image-wrap">
                                <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" loading="lazy">
                            </div>
                        </a>
                        <div class="info">
                            <div class="brand-name">{{ $product->brand->name ?? '' }}</div>
                            <div class="product-name"><a href="/san-pham/{{ $product->slug }}">{{ $product->name }}</a></div>
                            <div class="price-wrap">
                                <span class="current-price">{{ number_format($product->final_price) }}₫</span>
                                @if($product->sale_price)
                                    <span class="old-price">{{ number_format($product->price) }}₫</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Banner quảng cáo -->
        <section class="mb-40"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 16px; padding: 50px; text-align: center; color: #fff;">
            <h2 style="font-size: 32px; font-weight: 700; margin-bottom: 12px;">🔥 Flash Sale cuối tuần</h2>
            <p style="font-size: 18px; opacity: 0.9; margin-bottom: 24px;">Giảm đến 40% cho tất cả giày thể thao. Không thể
                bỏ lỡ!</p>
            <a href="/san-pham" class="btn" style="background: #fff; color: #764ba2;">Mua ngay</a>
        </section>

        <!-- Thương hiệu -->
        <section class="mb-40">
            <div class="section-title">
                <h2>Thương hiệu</h2>
            </div>
            <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px;">
                @foreach($brands as $brand)
                    <a href="/thuong-hieu/{{ $brand->slug }}"
                        style="background: #fff; border-radius: 12px; padding: 30px; text-align: center; text-decoration: none; box-shadow: 0 2px 10px rgba(0,0,0,0.06); transition: all 0.3s;">
                        <div style="font-size: 32px; font-weight: 800; color: #1a1a2e;">{{ $brand->name }}</div>
                        <p style="font-size: 13px; color: #999; margin-top: 8px;">{{ $brand->products()->active()->count() }}
                            sản phẩm</p>
                    </a>
                @endforeach
            </div>
        </section>
    </div>
@endsection