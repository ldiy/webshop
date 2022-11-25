<!DOCTYPE html>
<html lang="en">
<?php
template('head', ['title' => 'Order']);
?>
<body>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo url('/resources/css/view-order.css'); ?>">

<div class="d-flex flex-column min-vh-100">

    <!--  Header  -->
    <?php template('header'); ?>

    <!--  Main  -->
    <main class="flex-fill">
        <div class="container d-flex flex-row mt-4">
            <div class="flex-fill px-4">
                <div class="row mb-3 order-status">
                    <div class="col">
                        <h2>Order #<?php echo $order->id; ?></h2>
                        <p><span class="fw-bold">Date: </span><?php $date = date_create($order->created_at); echo date_format($date, 'Y/m/d'); ?></p>
                        <p><span class="fw-bold">Status: </span><?php echo $status; ?></p>
                        <p><span class="fw-bold">Total: </span>€ <?php echo $order->getTotalPrice(); ?></p>
                        <p>
                            <span class="fw-bold">Paid at: </span>
                            <?php
                            if (!is_null($order->paid_at)) {
                                $date = date_create($order->paid_at);
                                echo date_format($date, 'Y/m/d');
                            } else {
                                echo 'Not paid yet';
                            }
                            ?>
                        </p>
                    </div>
                    <div class="col">
                        <h2><?php if($status === 'shipped') echo 'Shipped to:'; else echo 'Ship to:'; ?></h2>
                        <p><?php echo htmlspecialchars($address->first_name . ' ' . $address->last_name); ?><br/>
                            <?php echo htmlspecialchars($address->address_line1); ?><br/>
                            <?php if($address->address_line2 != null) echo htmlspecialchars($address->address_line2) . '<br/>'; ?>
                            <?php echo htmlspecialchars($address->postcode . ' ' . $address->city); ?><br/>
                            <?php echo $address->getCountryName(); ?></p>
                    </div>
                </div>
                <div class="row gx-md-3  row-cols-xl-3 justify-content-center">
                    <table class="table table-striped" id="products-table">
                        <thead>
                        <tr>
                            <th scope="col">Thumbnail</th>
                            <th scope="col">Name</th>
                            <th scope="col" class="align-right">Price</th>
                            <th scope="col" class="align-right">Quantity</th>
                            <th scope="col" class="align-right">Total</th>
                        </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><img class="img-thumbnail img-preview" src="<?php echo url($product->thumbnail_path) ?>" alt="<?php echo htmlspecialchars($product->name); ?>"></td>
                                    <td><?php echo htmlspecialchars($product->name); ?></td>
                                    <td class="align-right ">€ <?php echo number_format((float)$product->price, 2, '.', ''); ?></td>
                                    <td class="align-right"><?php echo $product->quantity; ?></td>
                                    <td class="align-right">€ <?php echo number_format((float)$product->price * $product->quantity, 2, '.', ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="border-top-0">
                                <td colspan="4" class="text-end">Subtotal:</td>
                                <td class="align-right">€ <?php echo $order->total_products; ?></td>
                            </tr>
                            <tr class="border-bottom-0">
                                <td colspan="4" class="text-end">Shipping:</td>
                                <td class="align-right">€ <?php echo $order->total_shipping; ?></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end">Tax:</td>
                                <td class="align-right">€ <?php echo $order->total_tax; ?></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total:</td>
                                <td class="align-right">€ <?php echo $order->getTotalPrice(); ?></td>
                            </tr>
                        </tfoot>
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