@extends('layouts.app')

@section('title', 'Sản phẩm')

@section('content')
    <div class="container" style="margin-top: 30px;">
        <ul class="breadcrumb">
            <li><a href="/">Trang chủ</a></li>
            <li>Sản phẩm</li>
        </ul>

        <div style="display: grid; grid-template-columns: 260px 1fr; gap: 30px;">
            <!-- Sidebar Filter -->
            <aside class="sidebar">
                <h3>Bộ lọc</h3>

                <form action="/san-pham" method="GET" id="filter-form">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <input type="hidden" name="sort" value="{{ request('sort', 'latest') }}" id="sort-input">

                    <!-- Danh mục -->
                    <div class="filter-group">
                        <h4>Danh mục</h4>
                        <a href="/san-pham" class="{{ !request('category') ? 'active' : '' }}">Tất cả</a>
                        @foreach($categories as $cat)
                            <a href="{{ request()->fullUrlWithQuery(['category' => $cat->slug, 'page' => null]) }}"
                                class="{{ request('category') == $cat->slug ? 'active' : '' }}">
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>

                    <!-- Thương hiệu -->
                    <div class="filter-group">
                        <h4>Thương hiệu</h4>
                        <a href="/san-pham" class="{{ !request('brand') ? 'active' : '' }}">Tất cả</a>
                        @foreach($brands as $brand)
                            <a href="{{ request()->fullUrlWithQuery(['brand' => $brand->slug, 'page' => null]) }}"
                                class="{{ request('brand') == $brand->slug ? 'active' : '' }}">
                                {{ $brand->name }}
                            </a>
                        @endforeach
                    </div>

                    <!-- Size -->
                    <div class="filter-group">
                        <h4>Kích cỡ</h4>
                        <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                            @foreach($sizes as $size)
                                <a href="{{ request()->fullUrlWithQuery(['size' => $size, 'page' => null]) }}"
                                    style="display: inline-block; padding: 6px 14px; border: 2px solid {{ request('size') == $size ? '#e94560' : '#e0e0e0' }}; border-radius: 6px; font-size: 13px; text-decoration: none; color: {{ request('size') == $size ? '#fff' : '#333' }}; background: {{ request('size') == $size ? '#e94560' : '#fff' }};">
                                    {{ $size }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Khoảng giá -->
                    <div class="filter-group">
                        <h4>Khoảng giá</h4>
                        <div style="display: flex; gap: 8px;">
                            <input type="number" name="min_price" class="form-control" placeholder="Từ"
                                value="{{ request('min_price') }}" style="padding: 8px; font-size: 13px;">
                            <input type="number" name="max_price" class="form-control" placeholder="Đến"
                                value="{{ request('max_price') }}" style="padding: 8px; font-size: 13px;">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm w-full mt-20"
                            style="justify-content: center;">Áp dụng</button>
                    </div>
                </form>
            </aside>

            <!-- Product List -->
            <div>
                <!-- Header -->
                <div
                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
                    <div>
                        <span style="font-size: 14px; color: #666;">Tìm thấy <strong>{{ $products->total() }}</strong> sản
                            phẩm</span>
                        @if(request('search'))
                            <span style="font-size: 14px; color: #666;"> cho "{{ request('search') }}"</span>
                        @endif
                    </div>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <label style="font-size: 14px; color: #666;">Sắp xếp:</label>
                        <select class="form-control" style="width: auto; padding: 8px 12px;"
                            onchange="window.location.href=this.value">
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'latest', 'page' => null]) }}" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Mới nhất</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_asc', 'page' => null]) }}" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá: Thấp đến cao</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_desc', 'page' => null]) }}" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá: Cao đến thấp</option>
                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'name_asc', 'page' => null]) }}" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên: A-Z</option>
                        </select>
                    </div>
                </div>

                @if($products->count() > 0)
                    <div class="product-grid">
                        @foreach($products as $product)
                            <div class="product-card">
                                @if($product->sale_price)
                                    <span class="badge">-{{ $product->sale_percentage }}%</span>
                                @endif
                                <a href="/san-pham/{{ $product->slug }}">
                                    <div class="image-wrap">
                                        <img src="{{ $product->thumbnail_url }}" alt="{{ $product->name }}" loading="lazy">
                                    </div>
                                </a>
                                <div class="info">
                                    <div class="brand-name">{{ $product->brand->name ?? '' }}</div>
                                    <div class="product-name"><a href="/san-pham/{{ $product->slug }}">{{ $product->name }}</a>
                                    </div>
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

                    {{ $products->links('partials.pagination') }}
                @else
                    <div style="text-align: center; padding: 60px 20px; background: #fff; border-radius: 12px;">
                        <div style="font-size: 64px; margin-bottom: 16px;">😕</div>
                        <h3 style="margin-bottom: 8px;">Không tìm thấy sản phẩm</h3>
                        <p style="color: #666; margin-bottom: 20px;">Không có sản phẩm nào phù hợp với bộ lọc của bạn.</p>
                        <a href="/san-pham" class="btn btn-primary">Xem tất cả sản phẩm</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection