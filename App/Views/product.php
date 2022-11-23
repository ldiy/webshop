<!DOCTYPE html>
<html lang="en">
<?php
template('head', ['title' => $product->name]);
?>
<body>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo url('/resources/css/product.css'); ?>">

<div class="d-flex flex-column min-vh-100">

    <!--  Header  -->
    <?php template('header'); ?>

    <!--  Main  -->
    <main class="flex-fill">
        <div class="container mt-5 mb-5">
            <div class="card">
                <div class="row g-0">
                    <div class="col-md-6">
                        <div class="d-flex flex-column justify-content-center">
                            <div class="main-image">
                                <img src="<?php if (isset($images[0])) echo url($images[0]->image_path); else echo url('/resources/img/photos/empty-img.png') ?>" id="main-product-image" alt="TODO">
                            </div>
                            <div class="thumbnail-images">
                                <?php foreach ($images as $image): ?>
                                    <div>
                                        <img onclick="changeImage(this)" src="<?php echo url($image->image_path); ?>" alt="<?php echo $image->alt; ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-3 right-side">
                            <h1 class="mb-3"><?php echo $product->name; ?></h1>
                            <hr>

                            <!--  Product properties  -->
                            <div class="text-muted">
                                <ul>
                                    <li>Stock: <?php echo $product->stock_quantity; ?> items</li>
                                    <li>Size:
                                        <?php
                                        if ($product->width < 1)
                                            echo $product->width * 100 . ' cm';
                                        else
                                            echo round($product->width,2) . ' m';

                                        if ($product->height < 1)
                                            echo ' x ' . $product->height * 100 . ' cm';
                                        else
                                            echo ' x ' . round($product->height,2) . ' m';

                                        if ($product->depth < 1)
                                            echo ' x ' . $product->depth * 100 . ' cm';
                                        else
                                            echo ' x ' . round($product->depth,2) . ' m';;
                                        ?>
                                    </li>
                                    <li>Weight:
                                        <?php
                                        if ($product->weight < 1)
                                            echo $product->weight * 1000 . ' g';
                                        else
                                            echo round($product->weight,2) . ' kg';
                                        ?>
                                    </li>
                                    <li>EAN: <?php echo $product->ean13; ?></li>
                                </ul>
                            </div>

                            <!-- Price -->
                            <h2 class="fw-bold">€ <?php echo number_format((float)$product->price, 2, '.', '');?></h2>
                            <p>Excl. VAT (21%): € <?php echo number_format((float)$product->price / 1.21, 2, '.', ''); ?></p>

                            <!-- Out of stock -->
                            <?php if($product->stock_quantity < 1): ?>
                            <h3><span class="badge bg-danger shadow">Out of stock</span></h3>
                            <?php endif; ?>

                            <!-- Add to cart -->
                            <form id="add-form" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-4">
                                            <input type="number" class="form-control form-control-lg quantity-input" name="quantity" id="quantity" value="1" min="1" max="9999999999">
                                    </div>
                                    <div class="col-8">
                                        <?php if (auth()->check()): ?>
                                            <button type="submit" id="submit-btn" class="btn btn-primary btn-lg">Add to cart</button>
                                        <?php else: ?>
                                            <a href="<?php echo url('/login'); ?>" class="btn btn-primary btn-lg">Login to add to cart</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </form>
                            <div class="alert alert-danger mt-3 visually-hidden" id="error" role="alert">
                                Something went wrong. Please try again later.
                            </div>

                            <!-- Description -->
                            <div class="description mt-4">
                                <p><?php echo $product->description; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- Modal successfully added -->
        <div class="modal fade" id="added-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?php echo $product->name ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        You have added <?php echo $product->name; ?> to your cart.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Continue shopping</button>
                        <button type="button" class="btn btn-primary" onclick="location.href = '<?php echo url('/cart'); ?>'">Show cart</button>
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
    function changeImage(element) {
        let mainImage = document.getElementById('main-product-image');
        mainImage.src = element.src;
    }

    $(document).ready(function () {
        $("#add-form").submit(function (event) {
            event.preventDefault();

            // Disable the submit button
            $("#submit-btn").prop("disabled", true);

            var data = {
                quantity: $("#quantity").val(),
                productId: <?php echo $product->id; ?>
            };
            $.ajax({
                type: "POST",
                enctype: 'multipart/form-data',
                url: "<?php echo url('/cart/add') ?>",
                accepts: "application/json",
                data: data,
                success: function (data) {
                    $("#added-modal").modal('show');
                    console.log("SUCCESS : ", data);
                    $("#submit-btn").prop("disabled", false);

                },
                error: function (e) {
                    $("#error").removeClass('visually-hidden');
                    $("#submit-btn").prop("disabled", false);
                }
            });
        });
    });

</script>

</body>
</html>