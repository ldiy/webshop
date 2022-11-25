<!DOCTYPE html>
<html lang="en">
<?php
template('head', ['title' => 'Orders - Admin']);
?>
<body>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo url('/resources/css/sidebar.css'); ?>">
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
            ['name' => 'Orders', 'url' => url('/admin/order')],
        ]]); ?>
        <div class="container d-flex flex-row mt-4">
            <?php template('admin-sidebar'); ?>
            <div class="flex-fill px-4">
                <h2>New orders <span class="badge rounded-pill bg-primary"><?php echo count($ordersToShip); ?></span></h2>
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
                    <?php foreach ($ordersToShip as $order): ?>
                        <tr onclick="location.href='<?php echo url('/admin/order/' . $order->id); ?>'" class="table-row">
                            <td>#<?php echo $order->id; ?></td>
                            <td><?php echo $order->getStatusName(); ?></td>
                            <td><?php $date = date_create($order->created_at); echo date_format($date, 'Y/m/d'); ?></td>
                            <td class="align-right">€ <?php echo number_format((float)$order->total_products, 2, '.', ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if(count($ordersToShip) === 0): ?>
                    <div class="col-12">
                        <p class="text-center">No orders that need to be shipped.</p>
                    </div>
                <?php endif; ?>

                <hr>
                <h2>Shipped: </h2>
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
                    <?php foreach ($shippedOrders as $order): ?>
                        <tr onclick="location.href='<?php echo url('/admin/order/' . $order->id); ?>'" class="order-row">
                            <td>#<?php echo $order->id; ?></td>
                            <td><?php echo $order->getStatusName(); ?></td>
                            <td><?php $date = date_create($order->created_at); echo date_format($date, 'Y/m/d'); ?></td>
                            <td class="align-right">€ <?php echo number_format((float)$order->total_products, 2, '.', ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if(count($shippedOrders) === 0): ?>
                    <div class="col-12">
                        <p class="text-center">No orders that are shipped.</p>
                    </div>
                <?php endif; ?>
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