<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use Core\Exceptions\HttpNotFoundException;
use Core\Http\Request;
use Core\Http\Response;

class ProductController
{
    public function show(Request $request, int $id) {
        $product = Product::find($id);
        if ($product === null) {
            throw new HttpNotFoundException('This product does not exist.');
        }
        return view('product', [
            'product' => $product,
        ]);
    }

    public function search(Request $request) {
        // TODO: orwhere description
        // TODO: pagination
        $search = $request->input('search');
        $products = Product::where('name', 'LIKE', "%$search%");

        if (count($products) === 1) {
            return redirect('/product/' . $products[0]->id);
        }

        return view('browse', [
            'title' => 'Search results for "' . $search . '"',
            'products' => $products,
        ]);
    }

    public function showCategory(Request $request, int $id): Response
    {
        // TODO: pagination
        $category = Category::find($id);
        if ($category === null) {
            throw new HttpNotFoundException('This category does not exist.');
        }
//        $products = $category->products();
        $products = Product::all(); // TODO: use the category's products
        $categries = Category::all();
        return view('browse', [
            'title' => $category->name,
            'description' => $category->description,
            'products' => $products,
            'categories' => $categries
        ]);
    }
}