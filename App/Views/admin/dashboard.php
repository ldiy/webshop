<!DOCTYPE html>
<html lang="en">
<?php
template('head', ['title' => 'Dashboard - Admin']);
?>
<body>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo url('/resources/css/admin.css'); ?>">

<div class="d-flex flex-column min-vh-100">

    <!--  Header  -->
    <?php template('header'); ?>

    <!--  Main  -->
    <main class="flex-fill">
        <!-- Breadcrumb -->
        <?php template('breadcrumb', ['items' => [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Admin', 'url' => url('/admin')],
        ]]); ?>
        <div class="container d-flex flex-row mt-4">
            <?php template('admin-sidebar'); ?>
            <div class="flex-fill px-4">
                <div class="row">
                    <div class="col-md-6 col-xl-3 mb-4">
                        <div class="card shadow border-start-primary py-2 clickable" onclick="location.href='<?php echo url('/admin/order'); ?>'">
                            <div class="card-body">
                                <div class="row align-items-center no-gutters">
                                    <div class="col me-2">
                                        <div class="text-uppercase text-primary fw-bold text-xs mb-1"><span>Orders</span></div>
                                        <div class="text-dark fw-bold h5 mb-0"><span><?php echo $totalOrders; ?></span></div>
                                    </div>
                                    <div class="col-auto"><i class="fa-solid fa-basket-shopping fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 mb-4">
                        <div class="card shadow border-start-success py-2">
                            <div class="card-body">
                                <div class="row align-items-center no-gutters">
                                    <div class="col me-2">
                                        <div class="text-uppercase text-success fw-bold text-xs mb-1"><span>Revenue</span></div>
                                        <div class="text-dark fw-bold h5 mb-0"><span>€ <?php echo $totalRevenue; ?></span></div>
                                    </div>
                                    <div class="col-auto"><i class="fa-solid fa-money-bill-trend-up fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 mb-4">
                        <div class="card shadow border-start-info py-2 clickable"  onclick="location.href='<?php echo url('/admin/user'); ?>'">
                            <div class="card-body">
                                <div class="row align-items-center no-gutters">
                                    <div class="col me-2">
                                        <div class="text-uppercase text-info fw-bold text-xs mb-1"><span>Customers</span></div>
                                        <div class="row g-0 align-items-center">
                                            <div class="col-auto">
                                                <div class="text-dark fw-bold h5 mb-0 me-3"><span><?php echo $totalUsers; ?></span></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto"><i class="fa-solid fa-user fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 mb-4">
                        <div class="card shadow border-start-warning py-2 clickable"  onclick="location.href='<?php echo url('/admin/product'); ?>'">
                            <div class="card-body">
                                <div class="row align-items-center no-gutters">
                                    <div class="col me-2">
                                        <div class="text-uppercase text-warning fw-bold text-xs mb-1"><span>Products</span></div>
                                        <div class="text-dark fw-bold h5 mb-0"><span><?php echo $totalProducts; ?></span></div>
                                    </div>
                                    <div class="col-auto"><i class="fa-solid fa-barcode fa-2x text-gray-300"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dashboard-item">
                    <h3>Latest pending orders</h3>
                    <hr class="delimiter">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th scope="col">Order #</th>
                            <th scope="col">Status</th>
                            <th scope="col">Ordered at</th>
                            <th scope="col" class="align-right">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($newOrders as $order): ?>
                            <tr onclick="location.href='<?php echo url('/admin/order/' . $order->id); ?>'" class="table-row">
                                <td>#<?php echo $order->id; ?></td>
                                <td><?php echo $order->getStatusName(); ?></td>
                                <td><?php $date = date_create($order->created_at); echo date_format($date, 'Y/m/d'); ?></td>
                                <td class="align-right">€ <?php echo number_format((float)$order->total_products, 2, '.', ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if(count($newOrders) === 0): ?>
                        <div class="col-12">
                            <p class="text-center">No orders that need to be shipped.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="dashboard-item">
                    <h3>Top selling products</h3>
                    <hr class="delimiter">
                    <table class="table table-striped table-hover">
                        <thead>
                        <tr>
                            <th scope="col">Thumbnail</th>
                            <th scope="col">Name</th>
                            <th scope="col">Price</th>
                            <th scope="col">Stock</th>
                            <th scope="col">Orders</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($topSellingProducts as $product_):  $product = $product_['product']; $quantity = $product_['quantity'];?>
                            <tr>
                                <td><img class="img-thumbnail img-preview" src="<?php echo htmlspecialchars(url($product->thumbnail_path)); ?>" alt="<?php echo $product->name; ?>"></td>
                                <td><?php echo $product->name; ?></td>
                                <td><?php echo $product->price; ?></td>
                                <td><?php echo $product->stock_quantity; ?></td>
                                <td><?php echo $quantity; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if(count($topSellingProducts) === 0): ?>
                        <div class="col-12">
                            <p class="text-center">Their aren't any products sold yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!--  Footer  -->
    <?php template('footer'); ?>
</div>


<!-- Scripts -->
<?php template('bottomScripts'); ?>
</body>
</html>