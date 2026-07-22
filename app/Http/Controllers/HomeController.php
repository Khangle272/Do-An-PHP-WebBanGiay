<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::active()->featured()->with(['category', 'brand'])->latest()->take(8)->get();
        $newProducts = Product::active()->with(['category', 'brand'])->latest()->take(8)->get();
        $categories = Category::active()->get();
        $brands = Brand::active()->get();

        return view('home', compact('featuredProducts', 'newProducts', 'categories', 'brands'));
    }
}
