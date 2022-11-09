<?php

namespace App\Controllers;

use App\Models\Category;
use Core\Exceptions\HttpNotFoundException;
use Core\Exceptions\ValidationException;
use Core\Http\Request;
use Core\Http\Response;
use Throwable;

class CategoryController
{
    /**
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws Throwable
     */
    public function show(Request $request, int $id): Response
    {
        // TODO: pagination
        $category = Category::find($id);
        if ($category === null) {
            throw new HttpNotFoundException('This category does not exist.');
        }
        $products = $category->products();
        $subcategories = $category->subcategories();


        return view('browse', [
            'title' => $category->name,
            'description' => $category->description,
            'products' => $products,
            'categories' => $subcategories,
        ]);
    }
}