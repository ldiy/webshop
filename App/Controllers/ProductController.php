<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use Core\Exceptions\HttpNotFoundException;
use Core\Exceptions\ValidationException;
use Core\Http\Request;
use Core\Http\Response;
use Core\Validation\RuleBuilder as Rule;
use Throwable;

class ProductController
{
    /**
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws Throwable
     */
    public function show(Request $request, int $id): Response
    {
        $product = Product::find($id);
        if ($product === null) {
            throw new HttpNotFoundException('This product does not exist.');
        }

        // Get the product images and sort them by the order column.
        $images = $product->productPhotos();
        usort($images, function ($a, $b) {
            return $a->order <=> $b->order;
        });

        return view('product', [
            'product' => $product,
            'images' => $images
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function search(Request $request): Response
    {
        // TODO: orwhere description
        // TODO: pagination
        $search = $request->input('search');
        $products = Product::where('name', 'LIKE', "%$search%")->get();

        // If only one product is found, redirect to the product page.
        if (count($products) === 1) {
            return redirect('/product/' . $products[0]->id);
        }

        $categories = [];
        foreach ($products as $product) {
            $categories = array_merge($categories, $product->categories());
        }

        return view('browse', [
            'title' => 'Search results for "' . $search . '"',
            'products' => $products,
            'categories' => $categories
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function index(Request $request): Response
    {
        $products = Product::all();
        $categories = Category::all();
        return view('admin/products', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}