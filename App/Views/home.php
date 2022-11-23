<!DOCTYPE html>
<html lang="en">
<?php template('head', ['title' => 'Home']); ?>
<body>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo url('/resources/css/products.css'); ?>">

<div class="d-flex flex-column min-vh-100">

    <!--  Header  -->
    <?php template('header'); ?>

    <!--  Main  -->
    <main class="flex-fill">
        <!-- Categories -->
        <div class="container px-4 px-lg-5 mt-5">
            <h2 class="mb-4">Categories</h2>
            <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                <?php foreach ($categories as $category):
                    $thumbnail = is_null($category->thumbnail_path) ? 'https://via.placeholder.com/500' : url($category->thumbnail_path);
                    ?>
                    <div class="col mb-5">
                        <div class="card category-card h-100" onclick="location.href='<?php echo url('/product?category=' . $category->id); ?>'">
                            <img class="card-img-top" src="<?php echo $thumbnail; ?>" alt="<?php echo $category->name; ?>">
                            <div class="card-body">
                            </div>
                            <div class="card-footer text-center">
                                <div class="text-center">
                                    <h5 class="fw-bolder"><?php echo $category->name; ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Featured products -->
        <div class="container px-4 px-lg-5 mt-5">
            <h2 class="mb-4">Featured products</h2>
            <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="col mb-5">
                        <div class="card product-card h-100" onclick="location.href='<?php echo url('/product/' . $product->id); ?>'">
                            <img class="card-img-top" src="<?php echo url($product->thumbnail_path); ?>" alt="<?php echo $product->name; ?>" />
                            <h5><span class="position-absolute top-0 start-50 translate-middle badge bg-warning">Featured</span></h5>
                            <div class="card-body p-4">
                                <div class="text-center">
                                    <h5 class="fw-bolder"><?php echo $product->name; ?></h5>
                                </div>
                            </div>
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent text-center">
                                <h2><span class="badge shadow bg-primary">€ <?php echo $product->price; ?></span></h2>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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