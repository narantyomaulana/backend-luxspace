<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index(Request $request)
    {

        $products = Product::with(['galleries'])->latest()->get();
        return view('pages.frontend.index', compact('products'));
        
    }
    public function details(Request $request, $slug)
    {
        $product = Product::with(['galleries'])->where('slug', $slug)->firstOrFail();//fungsi firstOrFail jika slug urlnya salah maka muncul 404
        return view('pages.frontend.details',compact('product'));

    }
    public function cart(Request $request)
    {
        return view('pages.frontend.cart');

    }
    public function success(Request $request)
    {
        return view('pages.frontend.success');

    }
}
