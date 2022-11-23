<!DOCTYPE html>
<html lang="en">
<?php
template('head', ['title' => 'Browse']);
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
        <!-- Breadcrumb -->
        <?php template('breadcrumb', ['items' => [
                ['name' => 'Home', 'url' => url('/')],
                ['name' => 'Browse', 'url' => url('/product')],
        ]]); ?>
        <div class="container d-flex flex-row">
            <nav class="flex-fill sidebar">
                    <div class="mb-3" id="category-section">
                        <h4>Subcategories</h4>
                        <hr class="delimiter">
                        <div class="px-2" id="category-container">
                            <!-- Category links (See template) -->
                        </div>
                    </div>
                <form id="filter-form">
                    <h4>Price</h4>
                    <hr class="delimiter">
                    <div class="input-group mb-3 px-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text">€</span>
                        </div>
                        <input type="number" class="form-control" name="price_min" id="price_min" placeholder="min" min="0">
                        <input type="number" class="form-control" name="price_max" id="price_max" placeholder="max" min="0">
                    </div>

                    <h4>Weight</h4>
                    <hr class="delimiter">
                    <div class="input-group mb-3 px-2">
                        <input type="number" class="form-control" name="weight_min" id="weight_min" placeholder="min" min="0" step="0.001">
                        <input type="number" class="form-control" name="weight_max" id="weight_max" placeholder="max" min="0" step="0.001">
                        <div class="input-group-append">
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>

                    <h4>Size</h4>
                    <hr class="delimiter">
                    <div class="mb-3 px-2">
                        <h6>Width</h6>
                        <div class="input-group mb-1">
                            <input type="number" class="form-control" name="width_min" id="width_min" placeholder="min" min="0" step="0.001">
                            <input type="number" class="form-control" name="width_max" id="width_max" placeholder="max" min="0" step="0.001">
                            <div class="input-group-append">
                                <span class="input-group-text">m</span>
                            </div>
                        </div>
                        <h6>Height</h6>
                        <div class="input-group mb-1">
                            <input type="number" class="form-control" name="height_min" id="height_min" placeholder="min" min="0" step="0.001">
                            <input type="number" class="form-control" name="height_max" id="height_max" placeholder="max" min="0" step="0.001">
                            <div class="input-group-append">
                                <span class="input-group-text">m</span>
                            </div>
                        </div>
                        <h6>Depth</h6>
                        <div class="input-group">
                            <input type="number" class="form-control" name="depth_min" id="depth_min" placeholder="min" min="0" step="0.001">
                            <input type="number" class="form-control" name="depth_max" id="depth_max" placeholder="max" min="0" step="0.001">
                            <div class="input-group-append">
                                <span class="input-group-text">m</span>
                            </div>
                        </div>
                    </div>

                    <h4>Stock</h4>
                    <hr class="delimiter">
                    <div class="mb-4 px-2">
                        <input type="checkbox" class="form-check-input" id="stock">
                        <label class="form-check-label" for="stock">In stock</label>
                    </div>
                </form>
                <div>
                    <button class="btn btn-primary shadow d-block w-100" onclick="applyFilters()">Apply</button>
                </div>
            </nav>
            <div class="flex-fill px-4">
                <!-- Sort on -->
                <div class="float-end row">
                    <label class="col-sm-4 col-form-label sort-label" for="sort">Sort on:</label>
                    <div class="col-sm-8">
                        <select class="form-select" id="sort" oninput="applySort()">
                            <option value="name" selected>Name</option>
                            <option value="price-asc">Price asc</option>
                            <option value="price-desc">Price desc</option>
                        </select>
                    </div>
                </div>

                <h2 class="mb-4" id="title">Products</h2>
                    <p class="mb-4 visually-hidden" id="description">Description</p>

                <div class="col-12 visually-hidden" id="no-products-found">
                    <p class="text-center">No products found.</p>
                </div>

                <div class="alert alert-danger text-center visually-hidden" role="alert" id="warning">
                    Something went wrong. Please try again later.
                </div>

                <div class="row gx-md-3 row-cols-xl-3 justify-content-center" id="product-container">
                    <!-- Product cards (See template) -->
                </div>
            </div>
        </div>
    </main>

    <!--  Footer  -->
    <?php template('footer'); ?>
</div>

<template id="product-card">
    <div class="col mb-3">
        <div class="card product-card h-100">
            <img class="card-img-top" src="" alt="Product image">
            <h5 class="visually-hidden out-of-stock-label"><span class="position-absolute top-0 start-50 translate-middle badge bg-danger">Out of stock</span></h5>
            <div class="card-body p-4">
                <div class="text-center">
                    <h5 class="fw-bolder product-name">Product name</h5>
                </div>
            </div>
            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent text-center">
                <h2><span class="badge bg-primary shadow product-price">€ Product price</span></h2>
            </div>
        </div>
    </div>
</template>

<template id="category-link">
    <p class="category-link">
        Category name
    </p>
</template>

<!-- Scripts -->
<?php template('bottomScripts'); ?>
<script>
    // Data from PHP
    let baseUrl = '<?php echo url('/'); ?>';
</script>
<script src="<?php echo url('/resources/js/browse.js'); ?>"></script>

</body>
</html>