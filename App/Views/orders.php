<!DOCTYPE html>
<html lang="en">
<?php
template('head', ['title' => 'Orders']);
?>
<body>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo url('/resources/css/orders.css'); ?>">

<div class="d-flex flex-column min-vh-100">

    <!--  Header  -->
    <?php template('header'); ?>

    <!--  Main  -->
    <main class="flex-fill">
        <div class="container d-flex flex-row mt-4">
            <div class="flex-fill px-5">
                <div class="row gx-md-3  row-cols-xl-3 justify-content-center">
                    <table class="table table-striped table-hover" id="products-table">
                        <thead>
                        <tr>
                            <th scope="col">Order #</th>
                            <th scope="col">Status</th>
                            <th scope="col">Ordered at</th>
                            <th scope="col" class="align-right">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr onclick="location.href='<?php echo url('/order/' . $order->id); ?>'" class="order-row">
                                <td>#<?php echo $order->id; ?></td>
                                <td><?php echo $order->getStatusName(); ?></td>
                                <td><?php $date = date_create($order->created_at); echo date_format($date, 'Y/m/d'); ?></td>
                                <td class="align-right">€ <?php echo number_format((float)$order->total_products, 2, '.', ''); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
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