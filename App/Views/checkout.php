<!DOCTYPE html>
<html lang="en">
<?php
template('head', ['title' => 'cart']);

$existingAddress = count($addresses) > 0;
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
            <h1>Checkout</h1>
            <div class="row">
                <div class="col-9">
                    <form method="post" action="<?php echo url('/register'); ?>" id="shipping-address-form">
                        <fieldset class="border p-2 bg-light mb-3">
                            <legend class="mb-3">Shipping address</legend>
                            <div class="form-check mb-2">
                                <input type="radio" class="form-check-input" name="address_selector" id="existing-address" value="existing-address" oninput="toggleAddressFields()" <?php if($existingAddress) echo 'checked'; else echo 'disabled="disabled"'; ?>>
                                <label for="existing-address" class="form-check-label">Select an existing address</label>
                            </div>
                            <div class="mb-3">
                                <select class="form-select form-select-lg" name="address_id" id="existing-address-selector" <?php if(!$existingAddress) echo 'disabled="disabled"'; ?>>
                                    <?php foreach ($addresses as $address): ?>
                                        <option value="<?php echo $address->id; ?>"><?php echo $address->address_line1 . ', ' . $address->city . ' ' . $address->postcode; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-check mb-2">
                                <input type="radio" class="form-check-input" name="address_selector" id="new-address" value="new-address" oninput="toggleAddressFields()" <?php if(!$existingAddress) echo 'checked'; ?>>
                                <label for="new-address" class="form-check-label">Use a new address</label>
                            </div>
                            <div class="d-none" id="new-address-form">
                                <!-- First and lastname -->
                                <div class="row mb-2">
                                    <div class="col">
                                        <div class="form-floating">
                                            <input class="form-control" type="text" name="first_name" id="first_name" placeholder="First Name" maxlength="45" minlength="2" required>
                                            <label class="form-label" for="first_name">First name</label>
                                            <div class="invalid-feedback">
                                                Enter your first name. Must be between 2 and 45 characters.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-floating">
                                            <input class="form-control" type="text" name="last_name" id="last_name" placeholder="Last Name" maxlength="45" minlength="2" required>
                                            <label class="form-label" for="last_name">Last name</label>
                                            <div class="invalid-feedback">
                                                Enter your last name. Must be between 2 and 45 characters.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Address line 1 -->
                                <div class="mb-2 form-floating">
                                    <input class="form-control" type="text" name="address_line1" id="address_line1" placeholder="Address" oninput="recalculateShipping()" minlength="2" maxlength="128" required>
                                    <label class="form-label" for="address_line1">Address</label>
                                    <div class="invalid-feedback">
                                        Enter a valid first address line (street name + number). Must be between 2 and 128 characters.
                                    </div>
                                </div>

                                <!-- Address line 2 -->
                                <div class="mb-2 form-floating">
                                    <input class="form-control" type="text" name="address_line2" id="address_line2" placeholder="Address Line 2 (optional)" oninput="recalculateShipping()" minlength="2" maxlength="128">
                                    <label class="form-label" for="address_line2">Address Line 2 (optional)</label>
                                    <div class="invalid-feedback">
                                        Enter a valid second address line (apartment number, floor, etc.). Must be between 2 and 128 characters.
                                    </div>
                                </div>

                                <!-- Postal code and city -->
                                <div class="row mb-2">
                                    <div class="col-4">
                                        <div class="form-floating">
                                            <input class="form-control" type="text" name="postcode" id="postcode" placeholder="Postal code" oninput="recalculateShipping()" minlength="2" maxlength="10" required>
                                            <label class="form-label" for="postal_code">Postal code</label>
                                            <div class="invalid-feedback">
                                                Enter a valid postal code. Must be between 2 and 10 characters.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-8">
                                        <div class="form-floating">
                                            <input class="form-control" type="text" name="city" id="city" placeholder="City" oninput="recalculateShipping()" minlength="2" maxlength="64" required>
                                            <label class="form-label" for="city">City</label>
                                            <div class="invalid-feedback">
                                                Enter a valid city. Must be between 2 and 64 characters.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Country -->
                                <div class="mb-4 form-floating">
                                    <select class="form-select" aria-label="Country" id="country" name="country" oninput="recalculateShipping()" required>
                                        <?php foreach ($countries as $code=>$country) : ?>
                                            <option value="<?php echo $code; ?>"><?php echo $country; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label class="form-label" for="country">Country</label>
                                </div>
                            </div>

                            <!-- Calculate shipping button right aligned-->
                            <div class="mb-4">
                                <button type="button" class="btn btn-primary float-end" onclick="calculateShipping()">Calculate shipping</button>
                            </div>
                        </fieldset>
                    </form>
                </div>
                <div class="col-3">
                    <div class="card bg-light">
                        <div class="card-header text-center">
                            <h2 class="card-title">Overview</h2>
                        </div>
                        <div class="card-body">
                            <table class="table fs-5 mb-0">
                                <tr>
                                    <td>Subtotal</td>
                                    <td class="text-end fw-bold">€ <?php echo number_format((float)$total, 2, '.', ''); ?></td>
                                </tr>
                                <tr>
                                    <td>Shipping</td>
                                    <td class="text-end" id="shipping-cost">€ --</td>
                                </tr>
                                <tr class="border-bottom">
                                    <td>Tax (21%)</td>
                                    <td class="text-end" id="tax">€ --</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>Total</td>
                                    <td class="text-end" id="total">€ --</td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-primary btn-lg checkout-btn" id="order-btn" onclick="order()" disabled="disabled">Order</button>
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
    toggleAddressFields(); // Run on page load
    function toggleAddressFields() {
        if ($('#new-address').is(':checked')) {
            $('#new-address-form').removeClass('d-none');
            $('#existing-address-selector').addClass('d-none');
        } else {
            $('#existing-address-selector').removeClass('d-none');
            $('#new-address-form').addClass('d-none');
        }
    }

    function calculateShipping() {
        let data; // Data to send to the server

        // Check if it is a new address or an existing one
        if ($('#new-address').is(':checked')) {
            data = {
                'countryCode': $('#country').val(),
            }

            // Validate the form
            let form = document.getElementById('shipping-address-form');
            if (form.checkValidity() === false) {
                form.classList.add('was-validated');
                return;
            }
        } else {
            data = {
                'addressId': $('#existing-address-selector').val(),
            }
        }

        // Send the data to the server and get the shipping cost
        $.ajax({
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json"
            },
            url: '<?php echo url('/shipping/calculate'); ?>',
            type: 'POST',
            data: JSON.stringify(data),
            success: function (data) {
                $('#shipping-cost').html('€ ' + parseFloat(data.shippingCost).toFixed(2));
                $('#tax').html('€ ' + parseFloat(data.tax).toFixed(2));
                $('#total').html('€ ' + parseFloat(data.total).toFixed(2));
                $('#order-btn').removeAttr('disabled');
            },
            error: function (data) {
                $('#shipping-cost').html('Error');
                $('#order-btn').attr('disabled', 'disabled');
            }
        });
    }

    function recalculateShipping() {
        $('#order-btn').attr('disabled', 'disabled');
        $('#shipping-cost').html('€ --');
        $('#tax').html('€ --');
        $('#total').html('€ --');
    }

    function order() {
        let form = document.getElementById('shipping-address-form');
        let data = new FormData(form);
        $.ajax({
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json"
            },
            url: '<?php echo url('/order'); ?>',
            type: 'POST',
            data: JSON.stringify(Object.fromEntries(data)),
            success: function (data) {
                let orderId = data.order.id;
                window.location.href = '<?php echo url('/order/'); ?>' + orderId + '/pay';
            },
            error: function (data) {
                console.log(data);
                alert('Error');
            }
        });
    }
</script>
</body>
</html>