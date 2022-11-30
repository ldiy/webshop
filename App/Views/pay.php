<!DOCTYPE html>
<html lang="en">
<?php
template('head', ['title' => 'Pay Order #'.$order->id]);
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
                    <form action="<?php echo url('/order/' . $order->id . '/pay') ?>" method="post">
                        <h2>Pay for order #<?php echo $order->id; ?></h2>
                        <h4>€ <?php echo number_format((float)$order->total_products, 2, '.', ''); ?></h4>
                        <button type="submit" class="btn btn-primary w-100">Pay</button>
                    </form>
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