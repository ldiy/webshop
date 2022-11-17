<!DOCTYPE html>
<html lang="en">
<?php
template('head', ['title' => 'Products - Admin']);
?>
<body>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo url('/resources/css/products-admin.css'); ?>">
<link rel="stylesheet" href="<?php echo url('/resources/css/sidebar.css'); ?>">

<div class="d-flex flex-column min-vh-100">

    <!--  Header  -->
    <?php template('header'); ?>

    <!--  Main  -->
    <main class="flex-fill">
        <div class="container d-flex flex-row mt-4">
            <nav class="flex-fill sidebar">
                <a href="<?php echo url('/admin/users'); ?>" class="mb-3">Users</a>
                <a href="<?php echo url('/admin/orders'); ?>" class="mb-3">Orders</a>
            </nav>
            <div class="flex-fill px-4">
                <div class="row gx-md-3  row-cols-xl-3 justify-content-center">
                    <button class="btn btn-primary" onclick="addProductModal()">Add product</button>
                    <?php if(count($products) === 0): ?>
                        <div class="col-12">
                            <p class="text-center">No products found.</p>
                        </div>
                    <?php endif; ?>
                    <table class="table table-striped table-hover" id="products-table">
                        <thead>
                            <tr>
                                <th scope="col">Thumbnail</th>
                                <th scope="col">Name</th>
                                <th scope="col">Price</th>
                                <th scope="col">Stock</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!--  Footer  -->
    <?php template('footer'); ?>
</div>

<!-- Add product modal -->
<div class="modal fade modal-lg" id="add-product-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add a product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" enctype="multipart/form-data" class="needs-validation" id="add-product-form">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" minlength="2" maxlength="128" required>
                        <div class="invalid-feedback">
                            Enter a valid product name (max 128 characters).
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" max="999999999" required>
                        <div class="invalid-feedback">
                            Enter a valid price
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" maxlength="512"></textarea>
                        <div class="invalid-feedback">
                            Enter a valid description (max 512 characters).
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6>Size</h6>
                        <div class="input-group">
                            <input type="number" class="form-control" name="width" id="width" placeholder="width" min="0" max="999999" step="0.001" required>
                            <div class="input-group-prepend">
                                <span class="input-group-text">X</span>
                            </div>
                            <input type="number" class="form-control" name="height" id="height" placeholder="height" min="0" max="999999" step="0.001" required>
                            <div class="input-group-prepend">
                                <span class="input-group-text">X</span>
                            </div>
                            <input type="number" class="form-control" name="depth" id="depth" placeholder="depth" min="0" max="999999" step="0.001" required>
                            <div class="input-group-append">
                                <span class="input-group-text">m</span>
                            </div>
                            <div class="invalid-feedback">
                                Enter a valid size.
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <h6>Weight</h6>
                        <div class="input-group">
                            <input type="number" class="form-control" name="weight" id="weight" placeholder="weight" min="0" max="999999" step="0.001" required>
                            <div class="input-group-prepend">
                                <span class="input-group-text">kg</span>
                            </div>
                            <div class="invalid-feedback">
                                Enter a valid weight.
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="stock" class="form-label">Stock quantity</label>
                        <input type="number" class="form-control" id="stock" name="stock" step="1" min="0" max="999999999" required>
                        <div class="invalid-feedback">
                            Enter a valid stock quantity
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="ean13" class="form-label">EAN13</label>
                        <input type="text" class="form-control" id="ean13" name="ean13" minlength="13" maxlength="13" required>
                        <div class="invalid-feedback">
                            Enter a valid EAN13 number
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="category-select" class="form-label">Category</label>
                        <div id="category-select">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="thumbnail" class="form-label">Thumbnail</label>
                        <input class="form-control" type="file" id="thumbnail" name="thumbnail" accept="image/*" required>
                        <div class="invalid-feedback">
                            Upload a valid thumbnail.
                        </div>
                        <img src="https://via.placeholder.com/500" alt="" id="thumbnail-preview" class="img-thumbnail img-preview visually-hidden">
                    </div>
                    <div class="mb-3">
                        <label for="photos" class="form-label">Photos</label>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Preview</th>
                                    <th>Alt</th>
                                    <th class="action-col">Up</th>
                                    <th class="action-col">Delete</th>
                                </tr>
                            </thead>
                            <tbody id="photo-previews">

                            </tbody>
                        </table>

                        <div ondrop="drop(event)" ondragover="return false" onclick="file_explorer()" class="dropzone">
                            <p>Drag and drop image(s) here.</p>
                            <p>Or</p>
                            <p>Click to select image(s).</p>
                            <input class="form-control" type="file" id="photos" accept="image/*" multiple>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="alert alert-danger mt-3 visually-hidden" id="add-product-error" role="alert">
                    Something went wrong. Please try again later.
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetForm()">Close</button>
                <button type="button" class="btn btn-primary" id="submit-btn" onclick="addProduct()">Add</button>
            </div>
        </div>
    </div>
</div>

<template id="photo-template">
    <tr>
        <td>
            <img src="https://via.placeholder.com/500" alt="" class="img-thumbnail img-preview">
        </td>
        <td>
            <input class="form-control alt-input" type="text" name="alt[]" maxlength="64" required>
        </td>
        <td class="action-col">
            <button type="button" class="btn btn-primary move-up-btn">
                <i class="fa-solid fa-caret-up"></i>
            </button>
        </td>
        <td class="action-col">
            <button type="button" class="btn btn-danger remove-btn">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<template id="product-row-template">
    <tr>
        <td><img class="img-thumbnail img-preview" src="" alt=""></td>
        <td>Name</td>
        <td>Price</td>
        <td>Stock</td>
        <td>
            <button class="btn btn-primary">Edit</button>
            <button class="btn btn-danger">Delete</button>
        </td>
    </tr>
</template>


<!-- Scripts -->
<?php template('bottomScripts'); ?>
<script src="<?php echo url('/resources/js/tree.min.js'); ?>"></script>

<script>
    // Pass data from php to js
    let products = <?php echo json_encode($products); ?>;
    let categories = <?php echo json_encode($categories); ?>;
    let baseUrl = '<?php echo url('/'); ?>';
</script>

<script src="<?php echo url('/resources/js/products-admin.js'); ?>"></script>

</body>
</html>