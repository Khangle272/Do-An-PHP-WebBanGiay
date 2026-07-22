@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="container">
        <ul class="breadcrumb">
            <li><a href="/">Trang chủ</a></li>
            <li><a href="/san-pham">Sản phẩm</a></li>
            <li><a href="/danh-muc/{{ $product->category->slug ?? '' }}">{{ $product->category->name ?? '' }}</a></li>
            <li>{{ $product->name }}</li>
        </ul>

        <div class="product-detail">
            <!-- Gallery -->
            <div>
                <div class="gallery-main">
                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" id="main-image">
                </div>
                @if($product->images->count() > 0)
                    <div class="gallery-thumbs">
                        <img src="{{ $product->thumbnail }}" class="active" data-src="{{ $product->thumbnail }}"
                            alt="Thumbnail">
                        @foreach($product->images as $image)
                            <img src="{{ $image->image }}" data-src="{{ $image->image }}" alt="Product image" loading="lazy">
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Info -->
            <div class="detail-info">
                <div class="brand">{{ $product->brand->name ?? '' }}</div>
                <h1>{{ $product->name }}</h1>

                <div class="price">
                    {{ number_format($product->final_price) }}₫
                    @if($product->sale_price)
                        <span class="old-price">{{ number_format($product->price) }}₫</span>
                        <span
                            style="font-size: 14px; color: #e94560; font-weight: 600; background: #fde8e8; padding: 2px 10px; border-radius: 6px; margin-left: 8px;">-{{ $product->sale_percentage }}%</span>
                    @endif
                </div>

                @if($product->avg_rating > 0)
                    <div style="margin: 12px 0; font-size: 16px;">
                        ⭐ {{ $product->avg_rating }} ({{ $product->reviews->count() }} đánh giá)
                    </div>
                @endif

                <p class="desc">{{ $product->description }}</p>

                <form action="/gio-hang/them" method="POST" id="add-to-cart-form">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="product_size_id" id="selected-size" value="">
                    <input type="hidden" name="product_color_id" id="selected-color" value="">

                    <!-- Size -->
                    @if($product->sizes->count() > 0)
                        <div style="margin: 16px 0;">
                            <strong style="font-size: 14px;">Kích cỡ:</strong>
                            <div class="size-options">
                                @foreach($product->sizes as $size)
                                    <button type="button" data-value="{{ $size->id }}" {{ $size->stock == 0 ? 'disabled style=opacity:0.4;cursor:not-allowed;' : '' }}>
                                        {{ $size->size }}
                                        @if($size->stock == 0) (Hết) @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Color -->
                    @if($product->colors->count() > 0)
                        <div style="margin: 16px 0;">
                            <strong style="font-size: 14px;">Màu sắc:</strong>
                            <div class="color-options">
                                @foreach($product->colors as $color)
                                    <button type="button" data-value="{{ $color->id }}"
                                        style="background: {{ $color->color_code }}; border-color: {{ $color->color_code == '#FFFFFF' ? '#ccc' : $color->color_code }};"
                                        title="{{ $color->color_name }}"></button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Quantity -->
                    <div class="qty-input">
                        <strong style="font-size: 14px;">Số lượng:</strong>
                        <button type="button"
                            onclick="document.getElementById('qty').stepDown(); document.getElementById('qty').dispatchEvent(new Event('change'))"
                            style="padding: 6px 14px; border: 2px solid #e0e0e0; border-radius: 6px; background: #fff; cursor: pointer; font-size: 18px;">−</button>
                        <input type="number" name="quantity" id="qty" value="1" min="1" max="99" readonly
                            style="width: 50px; text-align: center; border: 2px solid #e0e0e0; border-radius: 6px; padding: 6px; font-size: 16px;">
                        <button type="button"
                            onclick="document.getElementById('qty').stepUp(); document.getElementById('qty').dispatchEvent(new Event('change'))"
                            style="padding: 6px 14px; border: 2px solid #e0e0e0; border-radius: 6px; background: #fff; cursor: pointer; font-size: 18px;">+</button>
                    </div>

                    <div style="display: flex; gap: 12px; margin-top: 24px;">
                        <button type="submit" class="btn btn-primary" style="padding: 14px 40px; font-size: 16px;">🛒 Thêm
                            vào giỏ hàng</button>
                        <button type="button" class="btn btn-outline wishlist-btn" data-product-id="{{ $product->id }}"
                            style="padding: 14px 20px; font-size: 16px;">
                            @auth
                                @php $wished = auth()->user()->wishlists()->where('product_id', $product->id)->exists(); @endphp
                                {{ $wished ? '❤️' : '🤍' }}
                            @else
                                🤍
                            @endauth
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reviews -->
        <section style="margin: 40px 0;">
            <h2 style="font-size: 22px; font-weight: 700; margin-bottom: 20px;">Đánh giá sản phẩm</h2>

            @auth
                @php
                    $hasBought = auth()->user()->orders()->whereHas('items', function ($q) use ($product) {
                        $q->where('product_id', $product->id);
                    })->exists();
                    $hasReviewed = auth()->user()->reviews()->where('product_id', $product->id)->exists();
                @endphp
                @if($hasBought && !$hasReviewed)
                    <div
                        style="background: #fff; border-radius: 12px; padding: 24px; margin-bottom: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.06);">
                        <h3 style="font-weight: 600; margin-bottom: 16px;">Viết đánh giá</h3>
                        <form method="POST" action="/san-pham/{{ $product->slug }}/danh-gia">
                            @csrf
                            <div class="form-group">
                                <label>Đánh giá của bạn</label>
                                <div class="star-rating">
                                    <input type="radio" name="rating" value="5" id="star5"><label for="star5">★</label>
                                    <input type="radio" name="rating" value="4" id="star4"><label for="star4">★</label>
                                    <input type="radio" name="rating" value="3" id="star3"><label for="star3">★</label>
                                    <input type="radio" name="rating" value="2" id="star2"><label for="star2">★</label>
                                    <input type="radio" name="rating" value="1" id="star1" checked><label for="star1">★</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="comment">Nhận xét</label>
                                <textarea name="comment" id="comment" class="form-control"
                                    placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..." maxlength="500"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                        </form>
                    </div>
                @endif
            @else
                <p style="margin-bottom: 20px; color: #666;">
                    <a href="/dang-nhap" style="color: #e94560;">Đăng nhập</a> để viết đánh giá.
                </p>
            @endauth

            @if($product->reviews->count() > 0)
                <div style="background: #fff; border-radius: 12px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,0.06);">
                    @foreach($product->reviews as $review)
                        <div class="review-card">
                            <div class="user">
                                {{ $review->user->name ?? 'Ẩn danh' }}
                                <span class="date">{{ $review->created_at->format('d/m/Y') }}</span>
                            </div>
                            <div class="stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <span style="color: {{ $i <= $review->rating ? '#f59e0b' : '#ddd' }};">★</span>
                                @endfor
                            </div>
                            @if($review->comment)
                                <div class="comment">{{ $review->comment }}</div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p style="color: #999; text-align: center; padding: 40px;">Chưa có đánh giá nào.</p>
            @endif
        </section>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <section class="mb-40">
                <div class="section-title">
                    <h2>Sản phẩm liên quan</h2>
                    <a href="/danh-muc/{{ $product->category->slug ?? '' }}">Xem tất cả →</a>
                </div>
                <div class="product-grid">
                    @foreach($relatedProducts as $rp)
                        <div class="product-card">
                            @if($rp->sale_price)
                                <span class="badge">-{{ $rp->sale_percentage }}%</span>
                            @endif
                            <a href="/san-pham/{{ $rp->slug }}">
                                <div class="image-wrap"><img src="{{ $rp->thumbnail }}" alt="{{ $rp->name }}" loading="lazy"></div>
                            </a>
                            <div class="info">
                                <div class="brand-name">{{ $rp->brand->name ?? '' }}</div>
                                <div class="product-name"><a href="/san-pham/{{ $rp->slug }}">{{ $rp->name }}</a></div>
                                <div class="price-wrap">
                                    <span class="current-price">{{ number_format($rp->final_price) }}₫</span>
                                    @if($rp->sale_price)
                                        <span class="old-price">{{ number_format($rp->price) }}₫</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <script>
        // Gallery
        document.querySelectorAll('.gallery-thumbs img').forEach(thumb => {
            thumb.addEventListener('click', function () {
                document.getElementById('main-image').src = this.dataset.src;
                document.querySelectorAll('.gallery-thumbs img').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Size selection
        document.querySelectorAll('.size-options button').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.size-options button').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('selected-size').value = this.dataset.value;
            });
        });

        // Color selection
        document.querySelectorAll('.color-options button').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.color-options button').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('selected-color').value = this.dataset.value;
            });
        });

        // Add to cart validation
        document.getElementById('add-to-cart-form')?.addEventListener('submit', function (e) {
            @if(!auth()->check())
                e.preventDefault();
                alert('Vui lòng đăng nhập để thêm vào giỏ hàng!');
                window.location.href = '/dang-nhap';
            @endif
        });

        // Wishlist toggle
        document.querySelectorAll('.wishlist-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                @auth
                        const productId = this.dataset.productId;
                    fetch('/yeu-thich/toggle/' + productId, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        }
                    }).then(r => r.json()).then(data => {
                        this.innerHTML = data.wished ? '❤️' : '🤍';
                    });
                @else
                    window.location.href = '/dang-nhap';
                @endauth
            });
        });
    </script>
@endsection