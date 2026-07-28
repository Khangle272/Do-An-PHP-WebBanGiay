@extends('layouts.app')

@section('title', 'Sản phẩm yêu thích')

@section('content')
    <div class="container" style="margin-top: 30px; min-height: 60vh;">
        <ul class="breadcrumb">
            <li><a href="/">Trang chủ</a></li>
            <li>Yêu thích</li>
        </ul>

        <h1 style="font-size: 28px; font-weight: 700; margin-bottom: 30px;">❤️ Sản phẩm yêu thích</h1>

        @if($wishlist->count() > 0)
            <div class="product-grid">
                @foreach($wishlist as $item)
                    <div class="product-card">
                        @if($item->product->sale_price)
                            <span class="badge">-{{ $item->product->sale_percentage }}%</span>
                        @endif
                        <a href="/san-pham/{{ $item->product->slug }}">
                            <div class="image-wrap">
                                <img src="{{ $item->product->thumbnail_url }}" alt="{{ $item->product->name }}" loading="lazy">
                            </div>
                        </a>
                        <div class="info">
                            <div class="brand-name">{{ $item->product->brand->name ?? '' }}</div>
                            <div class="product-name"><a href="/san-pham/{{ $item->product->slug }}">{{ $item->product->name }}</a>
                            </div>
                            <div class="price-wrap">
                                <span class="current-price">{{ number_format($item->product->final_price) }}₫</span>
                                @if($item->product->sale_price)
                                    <span class="old-price">{{ number_format($item->product->price) }}₫</span>
                                @endif
                            </div>
                            <div style="display: flex; gap: 8px; margin-top: 12px;">
                                <a href="/san-pham/{{ $item->product->slug }}" class="btn btn-primary btn-sm">🛒 Thêm vào giỏ</a>
                                <form method="POST" action="/yeu-thich/toggle/{{ $item->product->id }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline btn-sm"
                                        style="color: #dc3545; border-color: #dc3545;">🗑️ Bỏ thích</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align: center; padding: 80px 20px; background: #fff; border-radius: 12px;">
                <div style="font-size: 64px; margin-bottom: 16px;">💔</div>
                <h2 style="margin-bottom: 8px;">Chưa có sản phẩm yêu thích</h2>
                <p style="color: #666; margin-bottom: 24px;">Hãy khám phá và thêm sản phẩm bạn yêu thích.</p>
                <a href="/san-pham" class="btn btn-primary">Khám phá ngay</a>
            </div>
        @endif
    </div>
@endsection