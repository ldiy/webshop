<?php

namespace App\Controllers;

use App\Models\Product;
use Core\Exceptions\ValidationException;
use Core\Http\JsonResponse;
use Core\Http\Request;
use Core\Http\Response;
use Core\Validation\RuleBuilder as Rule;
use Throwable;

class CartController
{
    /**
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function show(Request $request): Response
    {
        $cart = $request->session()->get('cart');
        $products = empty($cart) ? [] : Product::whereIn('id', array_keys($cart))->get();

        // Add quantity to products
        foreach ($products as $product) {
            $product->quantity = $cart[$product->id];
        }

        // Calculate total and tax TODO: do this when adding/removing items from the cart
        $total = 0;
        foreach ($products as $product) {
            $total += $product->price * $product->quantity;
        }
        $tax = $total - $total / 1.21;

        // Save the total and tax in the session
        $request->session()->set('cartTotal', $total);
        $request->session()->set('cartTax', $tax);

        return view('cart', [
            'items' => $products,
            'total' => $total,
            'tax' => $tax,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'productId' => Rule::new()->required()->numeric()->exists(Product::$table, Product::$primaryKey),
            'quantity' => Rule::new()->required()->numeric()->minValue(1),
        ]);

        // Get the current cart
        $cart = $request->session()->get('cart');

        // Add the new item or update the quantity
        $productId = $request->input('productId');
        $quantity = $request->input('quantity');
        if (isset($cart[$productId])) {
            $cart[$productId] += $quantity;
        } else {
            $cart[$productId] = $quantity;
        }

        // Save the cart
        $request->session()->set('cart', $cart);

        return jsonResponse([
            'success' => true,
            'message' => 'Product added to cart',
            'quantity' => $cart[$productId],
            'cart' => $cart,
        ], 201);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'productId' => Rule::new()->required()->numeric()->exists(Product::$table, Product::$primaryKey),
            'quantity' => Rule::new()->required()->numeric()->minValue(0),
        ]);

        // Get the current cart
        $cart = $request->session()->get('cart');

        // Update the quantity
        $productId = $request->input('productId');
        $quantity = $request->input('quantity');
        $previousQuantity = $cart[$productId];
        if ($quantity === 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = $quantity;
        }

        // Save the cart
        $request->session()->set('cart', $cart);

        // Find the product
        $product = Product::find($productId);

        // Update the total and tax
        $total = $request->session()->get('cartTotal');
        $total += ($product->price * $quantity) - ($product->price * $previousQuantity);
        $tax = $total - $total / 1.21;
        $request->session()->set('cartTotal', $total);
        $request->session()->set('cartTax', $tax);

        return jsonResponse([
            'success' => true,
            'message' => 'Product quantity updated',
            'total' => $total,
            'tax' => $tax,
            'totItemPrice' => $product->price * $quantity,
        ], 200);
    }
}