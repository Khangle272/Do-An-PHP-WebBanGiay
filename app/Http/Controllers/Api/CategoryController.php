<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        return CategoryResource::collection(Category::active()->get());
    }

    public function show($slug)
    {
        $category = Category::active()->where('slug', $slug)->firstOrFail();

        return new CategoryResource($category);
    }
}