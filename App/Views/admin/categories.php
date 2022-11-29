<!DOCTYPE html>
<html lang="en">
<?php
template('head', ['title' => 'Categories - Admin']);
?>
<body>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo url('/resources/css/sidebar.css'); ?>">
<link rel="stylesheet" href="<?php echo url('/resources/css/admin.css'); ?>">

<div class="d-flex flex-column min-vh-100">

    <!--  Header  -->
    <?php template('header'); ?>

    <!--  Main  -->
    <main class="flex-fill">
        <!-- Breadcrumb -->
        <?php template('breadcrumb', ['items' => [
            ['name' => 'Home', 'url' => url('/')],
            ['name' => 'Admin', 'url' => url('/admin')],
            ['name' => 'Categories', 'url' => url('/admin/category')],
        ]]); ?>
        <div class="container d-flex flex-row mt-4">
            <?php template('admin-sidebar'); ?>
            <div class="flex-fill px-4">
                <h2>Categories <span class="badge bg-primary clickable rounded-pill" onclick="showAddCategoryModal()"><i class="fa-solid fa-plus"></i></span></h2>
                <div id="category-container">
                    <template id="category-template">
                        <div class="category" data-id="">
                            <img src="" alt="Category thumbnail" class="category-thumbnail visually-hidden">
                            <h4 class="category-title">Title</h4>
                            <span class="delete-icon clickable" onclick="showDeleteCategoryModal(this)"><i class="fa-solid fa-trash"></i></span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </main>

    <!--  Footer  -->
    <?php template('footer'); ?>
</div>

<div class="modal fade modal-lg" id="add-category-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-title">Add a Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="post" enctype="multipart/form-data" class="needs-validation" id="add-product-form">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" minlength="2" maxlength="128" required>
                        <div class="invalid-feedback">
                            Enter a valid category name (max 128 characters).
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description (optional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3" maxlength="512"></textarea>
                        <div class="invalid-feedback">
                            Enter a valid description (max 512 characters).
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="parent-category" class="form-label">Parent Category</label>
                        <select class="form-select" id="parent-category" name="parent_id" required>
                            <option value="0" selected>No parent category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Select a valid parent category.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="thumbnail" class="form-label">Thumbnail (optional)</label><br>
                        <img src="https://via.placeholder.com/500" alt="" id="thumbnail-preview" class="img-thumbnail img-preview visually-hidden">
                        <input class="form-control" type="file" id="thumbnail" name="thumbnail" accept="image/*">
                        <div class="invalid-feedback">
                            Upload a valid thumbnail.
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="visibility" name="visibility" checked>
                            <label class="form-check-label" for="visibility">
                                Visible
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <div class="alert alert-danger mt-3 visually-hidden" id="add-product-error" role="alert">
                    Something went wrong. Please try again later.
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="resetForm()">Close</button>
                <button type="button" class="btn btn-primary" id="submit-btn" onclick="addCategory()">Add</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete confirmation modal -->
<div class="modal fade" id="delete-category-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the category "<span id="category-name" class="fw-bold"></span>", along with all its subcategories?<br>
                All products in those categories will be unlinked from it.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="delete-btn">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<?php template('bottomScripts'); ?>
<script>
    const categories = <?php echo json_encode($categories); ?>;
    const baseUrl = '<?php echo url(''); ?>';
    const categoryContainer = document.getElementById('category-container');

    let parsedCategories = parseCategories(categories);
    renderCategories(parsedCategories);

    // Update the thumbnail preview when a new file is selected
    $('#thumbnail').on('change', function () {
        let file = this.files[0];
        let reader = new FileReader();
        reader.onloadend = function () {
            $('#thumbnail-preview').attr('src', reader.result);
            $('#thumbnail-preview').removeClass('visually-hidden');
        }
        if (file) {
            reader.readAsDataURL(file);
        } else {
            $('#thumbnail-preview').addClass('visually-hidden');
        }
    });

    /**
     * Calculate the levels of the categories based on their parent_id's
     *
     * @param categories
     * @param parent
     * @param level
     * @returns {*[]}
     */
    function parseCategories(categories, parent = null, level = 0) {
        let parsedCategories = [];
        for (let category of categories) {
            if (category.parent_id === parent) {
                category.level = level;
                parsedCategories.push(category);
                parsedCategories = parsedCategories.concat(parseCategories(categories, category.id, level + 1));
            }
        }
        return parsedCategories;
    }

    /**
     * Render the categories in a tree structure, each level is indented
     *
     * @param categories
     */
    function renderCategories(categories) {
        let first = true;
        for (let category of categories) {
            let categoryDiv = document.importNode(document.getElementById('category-template').content, true).querySelector('.category');

            categoryDiv.dataset.id = category.id;
            categoryDiv.style.marginLeft = (category.level * 50) + 'px';
            if (category.level === 0 && !first) {
                categoryDiv.style.marginTop = '20px';
            }
            categoryDiv.querySelector('.category-title').textContent = category.name;
            if (category.thumbnail_path && category.level === 0) {
                categoryDiv.querySelector('.category-thumbnail').src = baseUrl + category.thumbnail_path;
                categoryDiv.querySelector('.category-thumbnail').classList.remove('visually-hidden');
            }

            // Go to category page on click
            categoryDiv.addEventListener('click', () => {
                showEditCategoryModal(category);
            });

            categoryDiv.querySelector('.delete-icon').addEventListener('click', (e) => {
                e.stopPropagation();
                showDeleteCategoryModal(category);
            });

            categoryContainer.appendChild(categoryDiv);
            first = false;
        }
    }

    /**
     * Reset the add/edit category form
     */
    function resetForm() {
        document.getElementById('add-product-form').reset();
        document.getElementById('thumbnail-preview').classList.add('visually-hidden');
        document.getElementById('add-product-form').classList.remove('was-validated');
    }

    /**
     * Show the add category modal
     */
    function showAddCategoryModal() {
        resetForm();
        $('#modal-title').text('Add a Category');
        $('#submit-btn').text('Add');
        $('#submit-btn').attr('onclick', 'addCategory()');
        $('#add-category-modal').modal('show');
    }

    /**
     * Show the edit category modal, and fill the form with the category data
     *
     * @param category
     */
    function showEditCategoryModal(category) {
        resetForm();
        $('#modal-title').text('Edit Category');
        $('#name').val(category.name);
        $('#description').val(category.description);
        let parentId = category.parent_id ? category.parent_id : 0;
        $('#parent-category').val(parentId);
        if (category.thumbnail_path) {
            $('#thumbnail-preview').attr('src', baseUrl + category.thumbnail_path);
            $('#thumbnail-preview').removeClass('visually-hidden');
        }
        if (!category.visibility) {
            $('#visibility').prop('checked', false);
        }

        $('#submit-btn').text('Save');
        $('#submit-btn').attr('onclick', 'editCategory(' + category.id + ')');
        $('#add-category-modal').modal('show');
    }


    /**
     * Show delete warning modal
     *
     * @param category
     */
    function showDeleteCategoryModal(category) {
        $('#delete-category-modal').modal('show');
        $('#delete-btn').attr('onclick', 'deleteCategory(' + category.id + ')');
        $('#category-name').text(category.name);
    }

    /**
     * Add a new category, if the form is valid
     * When the request is successful, the page is reloaded
     */
    function addCategory() {
        let form = document.getElementById('add-product-form');
        if (form.checkValidity() === false) {
            form.classList.add('was-validated');
            return;
        }
        let formData = new FormData(form);

        // If parent category is set to "No parent category", remove the parent_id field
        if (formData.get('parent_id') === '0') {
            formData.delete('parent_id');
        }

        $.ajax({
            url: baseUrl + '/admin/category',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                console.log(data);
                $('#add-category-modal').modal('hide');
                location.reload();
            },
            error: function (data) {
                console.log(data);
                $('#add-product-error').removeClass('visually-hidden');
            }
        });
    }

    /**
     * Delete a category.
     * When the request is successful, the page is reloaded
     *
     * @param id
     */
    function deleteCategory(id) {
        $.ajax({
            url: baseUrl + '/admin/category/' + id,
            type: 'DELETE',
            success: function (data) {
                console.log(data);
                $('#delete-category-modal').modal('hide');
                location.reload();
            },
            error: function (data) {
                console.log(data);
                $('#delete-category-error').removeClass('visually-hidden');
            }
        });
    }

    /**
     * Edit a category, if the form is valid.
     * When the request is successful, the page is reloaded
     *
     * @param id
     */
    function editCategory(id) {
        let form = document.getElementById('add-product-form');
        if (form.checkValidity() === false) {
            form.classList.add('was-validated');
            return;
        }
        let formData = new FormData(form);

        // If parent category is set to "No parent category", remove the parent_id field
        if (formData.get('parent_id') === '0') {
            formData.delete('parent_id');
        }

        $.ajax({
            url: baseUrl + '/admin/category/' + id,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                console.log(data);
                $('#add-category-modal').modal('hide');
                location.reload();
            },
            error: function (data) {
                console.log(data);
                $('#add-product-error').removeClass('visually-hidden');
            }
        });
    }
</script>
</body>
</html>