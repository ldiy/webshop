<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use Core\Http\Response;
use Throwable;

class HomeController
{
    /**
     * Show the home page.
     *
     * @return Response
     * @throws Throwable
     */
    public function show(): Response
    {
        $categories = Category::all();
        $featuredProducts = Product::all(); // TODO: Get featured products

        return view('home', [
            'categories' => $categories,
            'featuredProducts' => $featuredProducts
        ]);
    }
}