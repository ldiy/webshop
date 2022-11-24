<?php

namespace App\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\Product;
use Core\Exceptions\ValidationException;
use Core\Http\JsonResponse;
use Core\Http\Request;
use Core\Http\Response;
use Core\Validation\RuleBuilder as Rule;
use Throwable;

class OrderController
{
    /**
     * The shipping costs for each country.
     * For now, these are hardcoded. These could be stored in the database or fetched from a carrier API.
     * These are inclusive of VAT. and are in EUR.
     *
     * @var array|int[] [country_code => shipping_cost]
     */
    private static array $shippingCosts = [
        'BE' => 4.95,
        'NL' => 5.65,
        'DE' => 6.63,
        'FR' => 6.25,
        'UK' => 11.95,
    ];

    /**
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws Throwable
     */
    public function show(Request $request, int $id): Response
    {
        $order = Order::find($id);
        if ($order === null) {
            return redirect('/orders');
        }

        // Temporarily disable soft deletes, so we can see all products
        Product::setSoftDelete(false);
        $products = $order->products();
        Product::setSoftDelete(true);

        $address = $order->address();
        $status = $order->getStatusName();

        // Add data from the pivot table to the products
        foreach ($products as $product) {
            $product->quantity = $product->pivot['quantity'];
            $product->price = $product->pivot['unit_price'];
        }

        return view('viewOrder', [
            'order' => $order,
            'products' => $products,
            'address' => $address,
            'status' => $status,
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function index(Request $request): Response
    {
        $orders = auth()->user()->orders();
        return view('orders', [
            'orders' => $orders,
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function create(Request $request): Response
    {
        $cart = $request->session()->get('cart');
        $total = $request->session()->get('cartTotal');
        $tax = $request->session()->get('cartTax');

        // If the cart is empty, redirect back to the cart page
        if (is_null($cart) || count($cart) === 0) {
            return redirect('/cart');
        }

        // Get the user's addresses
        $addresses = auth()->user()->addresses();

        // Get all available countries to which we can ship
        $availableCountries = Address::getAvailableCountries();

        return view('checkout', [
            'cart' => $cart,
            'total' => $total,
            'tax' => $tax,
            'addresses' => $addresses,
            'countries' => $availableCountries,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'address_selector' => Rule::new()->required()->inArray(['new-address', 'existing-address']),
        ]);

        if ($request->input('address_selector') === 'new-address') {
            $request->validate([
                'first_name' => Rule::new()->required()->minLength(2)->maxLength(45),
                'last_name' => Rule::new()->required()->minLength(2)->maxLength(45),
                'address_line1' => Rule::new()->required()->minLength(2)->maxLength(128),
                'address_line2' => Rule::new()->nullable()->minLength(2)->maxLength(128),
                'city' => Rule::new()->required()->minLength(2)->maxLength(64),
                'postcode' => Rule::new()->required()->minLength(2)->maxLength(10),
                'country' => Rule::new()->required()->minLength(2)->maxLength(2)->inArray(array_keys(Address::getAvailableCountries())),
            ]);

            $address = Address::create([
                'user_id' => auth()->user()->id,
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'address_line1' => $request->input('address_line1'),
                'address_line2' => $request->input('address_line2'),
                'city' => $request->input('city'),
                'postcode' => $request->input('postcode'),
                'country_code' => $request->input('country'),
            ]);
        } else {
            $request->validate([
                'address_id' => Rule::new()->required()->numeric()->exists(Address::$table, 'id'),
            ]);

            $address = Address::find($request->input('address_id'));
        }

        // Get the cart information from the session
        $cart = $request->session()->get('cart');
        $total = $request->session()->get('cartTotal');
        $tax = $request->session()->get('cartTax');
        $shippingCost = $request->session()->get('cartShippingCost');

        // Check if the cart isn't empty
        if (empty($cart) || empty($total) || empty($tax) || is_null($shippingCost)) {
            throw ValidationException::fromMessages(['cart' => 'The cart is empty.']);
        }

        // Create the order
        $order = Order::create([
            'user_id' => auth()->user()->id,
            'address_id' => $address->id,
            'total_products' => $total,
            'total_tax' => $tax,
            'total_shipping' => $shippingCost,
            'status' => Order::findStatusCode('pending'),
        ]);

        // Find all the products in the database
        $products = Product::whereIn('id', array_keys($cart))->get();

        // Add the products to the order and decrease the stock
        foreach ($products as $product) {
            $order->attachProduct($product, $cart[$product->id]);
            $product->decrementStockQuantity($cart[$product->id]);
        }

        // Clear the cart
        $request->session()->remove('cart');
        $request->session()->remove('cartTotal');
        $request->session()->remove('cartTax');
        $request->session()->remove('cartShippingCost');

        return new JsonResponse([
            'success' => true,
            'message' => 'Order created successfully',
            'order' => $order,
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function calculateShipping(Request $request): Response
    {
        $request->validate([
            'countryCode' => Rule::new()->nullable()->inArray(array_keys(self::$shippingCosts)),
            'addressId' => Rule::new()->nullable()->numeric()->exists(Address::$table, Address::$primaryKey),
        ]);

        // Check if it is an existing address or a new one, and get the country code
        if ($request->input('addressId')) {
            $address = Address::find($request->input('addressId'));
            $countryCode = $address->country_code;
        } else {
            $countryCode = $request->input('countryCode');
        }

        // Calculate the shipping cost
        $shippingCost = self::$shippingCosts[$countryCode];

        // Set the shipping cost in the session
        $request->session()->set('cartShippingCost', $shippingCost);

        // Recalculate the total
        $cartTotal = $request->session()->get('cartTotal');
        $cartTax = $request->session()->get('cartTax');
        $total = $cartTotal + $shippingCost;
        $tax = $cartTax + ($shippingCost - ($shippingCost / 1.21));

        return jsonResponse([
            'shippingCost' => $shippingCost,
            'total' => $total,
            'tax' => $tax,
        ]);
    }
}