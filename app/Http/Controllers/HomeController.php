<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->take(6)->get();
        $categories = Category::latest()->take(4)->get();

        return view('welcome', compact('products', 'categories'));
    }
}
