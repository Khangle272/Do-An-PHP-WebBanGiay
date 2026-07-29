<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Toàn bộ $model->paginate()->links() trong app dùng markup
        // Bootstrap 4 (thuần <ul><li><a>) thay vì Tailwind mặc định,
        // vì CSS admin/khách hàng của project là CSS viết tay,
        // không load Tailwind nên view mặc định bị vỡ giao diện.
        Paginator::useBootstrapFour();
    }
}