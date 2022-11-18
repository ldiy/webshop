<!DOCTYPE html>
<html lang="en">
<?php
template('head', ['title' => 'cart']);
?>
<body>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo url('/resources/css/cart.css'); ?>">

<div class="d-flex flex-column min-vh-100">

    <!--  Header  -->
    <?php template('header'); ?>

    <!--  Main  -->
    <main class="flex-fill">
        <div class="container mt-5 mb-5">
            <h1>Shopping cart</h1>
            <div class="row">
                <div class="col-9">
                    <table class="table table-responsive">
                        <thead>
                            <tr>
                                <th scope="col">Product</th>
                                <th scope="col">Price</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item) : ?>
                                <tr>
                                    <td class="align-middle">
                                        <div class="d-flex">
                                            <img class="thumbnail" src="<?php echo url($item->thumbnail_path); ?>" alt="TODO">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h5><?php echo $item->name; ?></h5>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle price">
                                        <h5>€ <?php echo number_format((float)$item->price, 2, '.', ''); ?></h5>
                                    </td>
                                    <td class="align-middle">
                                        <input type="number" class="form-control form-control-lg quantity" oninput="updateQuantity(this, <?php echo $item->id; ?>)" value="<?php echo $item->quantity; ?>" min="0">
                                    </td>
                                    <td class="align-middle price">
                                        <h5 class="fw-bold total-item-price">€ <?php echo number_format((float)($item->price * $item->quantity), 2, '.', '');; ?></h5>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-3">
                    <div class="card bg-light">
                        <div class="card-header text-center">
                            <h2 class="card-title">Overview</h2>
                        </div>
                        <div class="card-body">
                            <table class="table fs-5 mb-0">
                                <tr class="border-bottom">
                                    <td>Tax (21%)</td>
                                    <td class="text-end" id="tax-total">€ <?php echo number_format((float)$tax, 2, '.', ''); ?></td>
                                </tr>
                                <tr>
                                    <td>Subtotal (incl tax)</td>
                                    <td class="text-end fw-bold" id="subtotal">€ <?php echo number_format((float)$total, 2, '.', ''); ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-footer">
                            <a href="<?php echo url('/checkout'); ?>" class="btn btn-primary btn-lg checkout-btn">Checkout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!--  Footer  -->
    <?php template('footer'); ?>
</div>

<!-- Scripts -->
<?php template('bottomScripts'); ?>

<script>
    function updateQuantity(element, id) {
        let quantity = parseInt(element.value);
        let data = {
            productId: id,
            quantity: quantity
        };
        $.ajax({
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json"
            },
            url: "<?php echo url('/cart/update'); ?>",
            data: JSON.stringify(data),
            type: 'POST',
            success: function (response) {
                if (quantity === 0) {
                    element.closest('tr').remove();
                } else {
                    let price = element.closest('tr').querySelector('.total-item-price');
                    price.innerHTML = '€ ' + (response.totItemPrice).toFixed(2);

                    let tax = document.getElementById('tax-total');
                    tax.innerHTML = '€ ' + response.tax.toFixed(2);

                    let subtotal = document.getElementById('subtotal');
                    subtotal.innerHTML = '€ ' + response.total.toFixed(2);
                }
            },
            error: function (response) {
                console.log(response);
            },
        });
    }
</script>
</body>
</html>