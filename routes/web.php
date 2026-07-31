<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WishlistController;

/*
|--------------------------------------------------------------------------
| Trang chủ
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| Auth - Đăng nhập / Đăng ký
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/dang-nhap', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/dang-nhap', [AuthController::class, 'login']);
    Route::get('/dang-ky', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/dang-ky', [AuthController::class, 'register']);

    // Quên mật khẩu
    Route::get('/quen-mat-khau', [AuthController::class, 'showForgotForm'])->name('password.request');
    Route::post('/quen-mat-khau', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/dat-lai-mat-khau/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/dat-lai-mat-khau', [AuthController::class, 'reset'])->name('password.update');
});

Route::post('/dang-xuat', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Sản phẩm - Danh mục - Thương hiệu
|--------------------------------------------------------------------------
*/
Route::get('/san-pham', [ProductController::class, 'index'])->name('products.index');
Route::get('/san-pham/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/danh-muc/{slug}', function ($slug) {
    return app(ProductController::class)->index(request()->merge(['category' => $slug]));
})->name('category.products');

Route::get('/thuong-hieu/{slug}', function ($slug) {
    return app(ProductController::class)->index(request()->merge(['brand' => $slug]));
})->name('brand.products');

/*
|--------------------------------------------------------------------------
| Đánh giá sản phẩm
|--------------------------------------------------------------------------
*/
Route::post('/san-pham/{slug}/danh-gia', [ReviewController::class, 'store'])->name('reviews.store');

/*
|--------------------------------------------------------------------------
| Giỏ hàng (Yêu cầu đăng nhập)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/gio-hang', [CartController::class, 'index'])->name('cart.index');
    Route::post('/gio-hang/them', [CartController::class, 'add'])->name('cart.add');
    Route::post('/gio-hang/cap-nhat/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/gio-hang/xoa/{id}', [CartController::class, 'remove'])->name('cart.remove');
});

/*
|--------------------------------------------------------------------------
| Đặt hàng (Checkout)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/dat-hang', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/dat-hang', [CheckoutController::class, 'placeOrder'])->name('checkout.place');
    Route::get('/dat-hang/thanh-cong/{id}', [CheckoutController::class, 'success'])->name('checkout.success');
});

/*
|--------------------------------------------------------------------------
| Đơn hàng
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/don-hang', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/don-hang/{id}', [OrderController::class, 'show'])->name('orders.show');
});

/*
|--------------------------------------------------------------------------
| Yêu thích (Wishlist)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/yeu-thich', [WishlistController::class, 'index'])->name('wishlist.index');
    Route::post('/yeu-thich/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
});

/*
|--------------------------------------------------------------------------
| Thông tin tài khoản
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/thong-tin', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/thong-tin', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/thong-tin/doi-mat-khau', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

/*
|--------------------------------------------------------------------------
| Admin - Quản trị (Yêu cầu Admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Sản phẩm
    Route::get('/san-pham', [App\Http\Controllers\Admin\ProductController::class, 'index'])->name('products.index');
    Route::get('/san-pham/tao', [App\Http\Controllers\Admin\ProductController::class, 'create'])->name('products.create');
    Route::post('/san-pham', [App\Http\Controllers\Admin\ProductController::class, 'store']);
    Route::get('/san-pham/{id}/sua', [App\Http\Controllers\Admin\ProductController::class, 'edit'])->name('products.edit');
    Route::post('/san-pham/{id}/sua', [App\Http\Controllers\Admin\ProductController::class, 'update']);
    Route::post('/san-pham/{id}/xoa', [App\Http\Controllers\Admin\ProductController::class, 'destroy'])->name('products.destroy');

    // Danh mục
    Route::get('/danh-muc', [App\Http\Controllers\Admin\CategoryController::class, 'index'])->name('categories.index');
    Route::get('/danh-muc/tao', [App\Http\Controllers\Admin\CategoryController::class, 'create'])->name('categories.create');
    Route::post('/danh-muc', [App\Http\Controllers\Admin\CategoryController::class, 'store']);
    Route::get('/danh-muc/{id}/sua', [App\Http\Controllers\Admin\CategoryController::class, 'edit'])->name('categories.edit');
    Route::post('/danh-muc/{id}/sua', [App\Http\Controllers\Admin\CategoryController::class, 'update']);
    Route::post('/danh-muc/{id}/xoa', [App\Http\Controllers\Admin\CategoryController::class, 'destroy'])->name('categories.destroy');

    // Thương hiệu
    Route::get('/thuong-hieu', [App\Http\Controllers\Admin\BrandController::class, 'index'])->name('brands.index');
    Route::get('/thuong-hieu/tao', [App\Http\Controllers\Admin\BrandController::class, 'create'])->name('brands.create');
    Route::post('/thuong-hieu', [App\Http\Controllers\Admin\BrandController::class, 'store']);
    Route::get('/thuong-hieu/{id}/sua', [App\Http\Controllers\Admin\BrandController::class, 'edit'])->name('brands.edit');
    Route::post('/thuong-hieu/{id}/sua', [App\Http\Controllers\Admin\BrandController::class, 'update']);
    Route::post('/thuong-hieu/{id}/xoa', [App\Http\Controllers\Admin\BrandController::class, 'destroy'])->name('brands.destroy');

    // Đơn hàng
    Route::get('/don-hang', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('/don-hang/{id}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
    Route::post('/don-hang/{id}/cap-nhat', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.update-status');

    // Người dùng
    Route::get('/nguoi-dung', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
});
