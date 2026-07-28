<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandResource;
use App\Models\Brand;

class BrandController extends Controller
{
    public function index()
    {
        return BrandResource::collection(Brand::active()->get());
    }

    public function show($slug)
    {
        $brand = Brand::active()->where('slug', $slug)->firstOrFail();

        return new BrandResource($brand);
    }
}