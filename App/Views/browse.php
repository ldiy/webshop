<!DOCTYPE html>
<html lang="en">
<?php
template('head', ['title' => $title]);
?>
<body>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo url('/resources/css/products.css'); ?>">
<link rel="stylesheet" href="<?php echo url('/resources/css/sidebar.css'); ?>">

<div class="d-flex flex-column min-vh-100">

    <!--  Header  -->
    <?php template('header'); ?>

    <!--  Main  -->
    <main class="flex-fill">
        <div class="container d-flex flex-row mt-4">
            <nav class="flex-fill sidebar">
                <?php if(!empty($categories)): ?>
                    <div class="mb-3">
                        <h4>Categories</h4>
                        <hr class="delimiter">
                        <div class="px-2">
                            <?php foreach ($categories as $category): ?>
                                <a href="<?php echo url('/category/' . $category->id); ?>" class="category-link">
                                    <?php echo $category->name; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <form>
                    <h4>Price</h4>
                    <hr class="delimiter">
                    <div class="input-group mb-3 px-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text">€</span>
                        </div>
                        <input type="text" class="form-control" name="price_min" id="price_min" placeholder="min">
                        <input type="text" class="form-control" name="price_max" id="price_max" placeholder="max">
                    </div>

                    <h4>Weight</h4>
                    <hr class="delimiter">
                    <div class="input-group mb-3 px-2">
                        <input type="text" class="form-control" name="weight_min" id="weight_min" placeholder="min">
                        <input type="text" class="form-control" name="weight_max" id="weight_max" placeholder="max">
                        <div class="input-group-append">
                            <span class="input-group-text">g</span>
                        </div>
                    </div>

                    <h4>Size</h4>
                    <hr class="delimiter">
                    <div class="mb-3 px-2">
                        <h6>Width</h6>
                        <div class="input-group mb-1">
                            <input type="text" class="form-control" name="width_min" id="width_min" placeholder="min">
                            <input type="text" class="form-control" name="width_max" id="width_max" placeholder="max">
                            <div class="input-group-append">
                                <span class="input-group-text">mm</span>
                            </div>
                        </div>
                        <h6>Height</h6>
                        <div class="input-group mb-1">
                            <input type="text" class="form-control" name="height_min" id="height_min" placeholder="min">
                            <input type="text" class="form-control" name="height_max" id="height_max" placeholder="max">
                            <div class="input-group-append">
                                <span class="input-group-text">mm</span>
                            </div>
                        </div>
                        <h6>Depth</h6>
                        <div class="input-group">
                            <input type="text" class="form-control" name="depth_min" id="depth_min" placeholder="min">
                            <input type="text" class="form-control" name="depth_max" id="depth_max" placeholder="max">
                            <div class="input-group-append">
                                <span class="input-group-text">mm</span>
                            </div>
                        </div>
                    </div>

                    <h4>Stock</h4>
                    <hr class="delimiter">
                    <div class="mb-4 px-2">
                        <input type="checkbox" class="form-check-input" id="stock">
                        <label class="form-check-label" for="stock">In stock</label>
                    </div>

                    <div>
                        <button class="btn btn-primary shadow d-block w-100" type="submit">Apply</button>
                    </div>
                </form>
            </nav>
            <div class="flex-fill px-4">
                <h2 class="mb-4"><?php echo htmlspecialchars($title); ?></h2>
                <?php if(isset($description)): ?>
                    <p class="mb-4"><?php echo $description; ?></p>
                <?php endif; ?>

                <div class="row gx-md-3  row-cols-xl-3 justify-content-center">
                    <?php if(count($products) === 0): ?>
                        <div class="col-12">
                            <p class="text-center">No products found.</p>
                        </div>
                    <?php endif; ?>
                    <?php foreach ($products as $product): ?>
                        <div class="col mb-3">
                            <div class="card product-card h-100" onclick="location.href='<?php echo url('/product/' . $product->id); ?>'">
                                <img class="card-img-top" src="<?php echo url($product->thumbnail_path); ?>" alt="<?php echo $product->name; ?>" />
                                <?php if($product->stock_quantity === 0): ?>
                                <h5><span class="position-absolute top-0 start-50 translate-middle badge bg-danger">Out of stock</span></h5>
                                <?php endif; ?>
                                <div class="card-body p-4">
                                    <div class="text-center">
                                        <h5 class="fw-bolder"><?php echo $product->name; ?></h5>
                                    </div>
                                </div>
                                <div class="card-footer p-4 pt-0 border-top-0 bg-transparent text-center">
                                    <h2><span class="badge bg-primary shadow">€ <?php echo $product->price; ?></span></h2>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>

    <!--  Footer  -->
    <?php template('footer'); ?>
</div>

<!-- Scripts -->
<?php template('bottomScripts'); ?>
<script src="<?php echo url('/resources/js/form-validation.js'); ?>"></script>

</body>
</html>