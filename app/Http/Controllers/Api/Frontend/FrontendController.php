<?php

namespace App\Http\Controllers\Api\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;

class FrontendController extends Controller
{
    public function category()
    {
        //get all posts
        $categories = Category::latest()->get();

        //return collection of posts as a resource
        return new CategoryResource(true, 'List Data Categories', 200, $categories);
    }

    public function product()
    {
        //get all posts
         $products = Product::with('category')->latest()->get();
        return new ProductResource(true, 'List Data Products', 200, $products);
    }

    public function productBySlug($slug)
    {
        // Cari produk berdasarkan slug
        $product = Product::where('slug', $slug)->first();

        if (!$product) {
            return new ProductResource(false, 'Product not found', 404, null);
        }

        // Return product sebagai resource
        return new ProductResource(true, 'Detail Product', 200, $product);
    }

}

