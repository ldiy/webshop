<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Order;
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
        $categories = Category::where('display', '=', 1)->whereNull('parent_id')->get();
        $featuredCategory = Category::getByName('Featured');
        $featuredProducts = !is_null($featuredCategory) ? $featuredCategory->products() : [];

        return view('home', [
            'categories' => $categories,
            'featuredProducts' => $featuredProducts
        ]);
    }

    /**
     * Show the admin dashboard.
     *
     * @return Response
     * @throws Throwable
     */
    public function admin(): Response
    {
        $totalUsers = count(Role::getByName('User')->users());
        $totalProducts = count(Product::all());
        $totalOrders = count(Order::all());
        $newOrders = Order::where('status', '=', Order::findStatusCode('pending'))->limit('10')->orderBy(Order::$createdAtColumn, 'desc')->get();

        $topSellingProducts = $this->getTopSellingProducts(10);
        $totalRevenue = $this->calculateTotalRevenue();

        return view('admin/dashboard', [
            'totalUsers' => $totalUsers,
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'totalRevenue' => $totalRevenue,
            'newOrders' => $newOrders,
            'topSellingProducts' => $topSellingProducts
        ]);
    }

    /**
     * Get the top-selling products.
     *
     * @param int $count The number of products to return.
     * @return array
     */
    private function getTopSellingProducts(int $count): array
    {
        $orders = Order::all();
        $topSellingProducts = [];

        foreach ($orders as $order) {
            foreach ($order->products() as $product) {
                if (array_key_exists($product->id, $topSellingProducts)) {
                    $topSellingProducts[$product->id]['quantity'] += $product->pivot['quantity'];
                }
                else {
                    $topSellingProducts[$product->id] = [
                        'product' => $product,
                        'quantity' => $product->pivot['quantity']
                    ];
                }
            }
        }

        usort($topSellingProducts, function ($a, $b) {
            return $b['quantity'] <=> $a['quantity'];
        });

        return array_slice($topSellingProducts, 0, $count);
    }

    /**
     * Calculate the total revenue from all orders.
     *
     * @return float
     */
    private function calculateTotalRevenue(): float
    {
        $orders = Order::all();
        $totalRevenue = 0;

        foreach ($orders as $order) {
            $totalRevenue += $order->getTotalPrice();
        }

        return $totalRevenue;
    }
}