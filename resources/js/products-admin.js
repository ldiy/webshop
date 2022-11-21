let treeData = parseCategories(categories);
let categoryTree;
let validatedImages = [];
let removedImages = [];
let imageCount = 0;

(function () {
    for (let product of products) {
        addToProductsTable(product);
    }
})();

/**
 * Add a product row to the table
 *
 * @param product
 * @param atTop
 */
function addToProductsTable(product, atTop = false) {
    let productTable = document.getElementById('products-table');
    let productTemplate = document.getElementById('product-row-template');
    let productRow = productTemplate.content.cloneNode(true);
    let productRowChildren = productRow.children[0].children;
    productRow.querySelector('tr').setAttribute('data-id', product.id);
    productRowChildren[0].children[0].src = product.thumbnail_path;
    productRowChildren[1].textContent = product.name;
    productRowChildren[2].textContent = product.price;
    productRowChildren[3].textContent = product.stock_quantity;
    if (atTop) {
        productTable.querySelector('tbody').prepend(productRow);
    } else {
        productTable.querySelector('tbody').append(productRow);
    }

    // Add event listeners
    productRowChildren[4].children[0].addEventListener('click', function() {
        editProductModal(product.id);
    });

    productRowChildren[4].children[1].addEventListener('click', function() {
        deleteProduct(product.id);
    });
}

/**
 * Remove a product row from the table
 *
 * @param id
 */
function removeFromProductsTable(id) {
    let productTable = document.getElementById('products-table');
    let productRow = productTable.querySelector(`tr[data-id="${id}"]`);
    productRow.remove();
}

/**
 * Edit a product row in the table
 *
 * @param id
 * @param product
 */
function editProductInProductsTable(id, product) {
    let productTable = document.getElementById('products-table');
    let productRow = productTable.querySelector(`tr[data-id="${id}"]`);
    let productRowChildren = productRow.children;
    productRowChildren[0].children[0].src = product.thumbnail_path;
    productRowChildren[1].textContent = product.name;
    productRowChildren[2].textContent = product.price;
    productRowChildren[3].textContent = product.stock_quantity;
}

/**
 * Parse the categories into a tree structure.
 *
 * @param categories
 * @param parent
 * @returns {*[]}
 */
function parseCategories(categories, parent = null) {
    let result = [];
    for (let category of categories) {
        if (category.parent_id === parent) {
            result.push({
                id: category.id,
                text: category.name,
                children: parseCategories(categories, category.id)
            });
        }
    }
    return result;
}

/**
 * Initialize the category tree.
 *
 * @param treeData
 */
function renderCategoryTree(treeData) {
    categoryTree = new Tree('#category-select', {
        closeDepth: 1,
        data: treeData,
        loaded: function () {
            this.collapseAll();
        },
        onChange: function() {
            console.log(this.values);
        },
    });
}

/**
 * Reset the tree to its initial state.
 */
function resetCategoryTree() {
    // Rerender the tree
    renderCategoryTree(treeData);
}

/**
 * File explorer.
 */
function file_explorer() {
    let photos = document.getElementById('photos');
    photos.click();
    photos.onchange = function (event) {
        let files = event.target.files;
        addFiles(files);
    };
}

/**
 * Drop event.
 *
 * @param event
 */
function drop(event) {
    event.preventDefault();
    let files = event.dataTransfer.files;
    addFiles(files);
}

/**
 * Add a file to list of images.
 * And display the preview.
 *
 * @param files
 */
function addFiles(files) {
    for (let file of files) {
        if (file.type.match('image.*')) {
            let id = 'image' + imageCount++;
            validatedImages.push({
                id: id,
                file: file,
                order: validatedImages.length,
                type: 'new'
            });
            let src = URL.createObjectURL(file);
            previewImage(src, id);
        }
    }
}

/**
 * Add a photo to the preview table.
 *
 * @param src
 * @param id
 * @param alt
 */
function previewImage(src, id, alt = '') {
    let template = document.getElementById('photo-template');
    let clone = template.content.cloneNode(true);
    clone.querySelector('img').src = src;
    clone.querySelector('tr').setAttribute('data-id', id);
    let altInput = clone.querySelector('input')
    altInput.name = 'alt[' + id + ']';
    altInput.value = alt;
    clone.querySelector('.move-up-btn').addEventListener('click', function () {
        moveImageUp(id);
    });
    clone.querySelector('.remove-btn').addEventListener('click', function () {
        removeImage(id);
    });

    document.getElementById('photo-previews').appendChild(clone);
}

/**
 * Remove a photo from the preview.
 *
 * @param id The id of the image.
 */
function removeImage(id) {
    let table = document.getElementById('photo-previews');
    let imageRow = table.querySelector(`tr[data-id="${id}"]`);
    let order;
    let index
    for (let image of validatedImages) {
        if (image.id === id) {
            order = image.order;
            index = validatedImages.indexOf(image);
            break;
        }
    }

    // Add to removed images array if it is an existing image.
    if (validatedImages[index].type === 'server') {
        removedImages.push(id);
    }

    // Remove the image from the array and table.
    validatedImages.splice(index, 1);
    imageRow.remove();

    // Update the order of the images.
    for (let image of validatedImages) {
        if (image.order > order) {
            image.order--;
        }
    }
}

/**
 * Move an image up.
 *
 * @param id The id of the image.
 */
function moveImageUp(id) {
    let index = validatedImages.findIndex(image => image.id === id);

    // Check if the image is already at the top.
    if (validatedImages[index].order === 0) {
        return;
    }

    // Find the image above.
    let aboveIndex = validatedImages.findIndex(image => image.order === validatedImages[index].order - 1);

    // Swap the order.
    validatedImages[index].order--;
    validatedImages[aboveIndex].order++;

    // Update the DOM.
    let table = document.getElementById('photo-previews');
    let row = table.querySelector(`tr[data-id="${id}"]`);
    let aboveRow = row.previousElementSibling;
    table.insertBefore(row, aboveRow);
}

/**
 * Validate the form
 *
 * @param form
 * @returns {boolean}
 */
function validateAddProduct (form) {
    form.classList.add('was-validated');
    return form.checkValidity();
}

/**
 * Add a product (request).
 */
function addProduct() {
    // Disable the submit button
    $("#submit-btn").prop("disabled", true);

    // Get the form
    let form = document.getElementById('add-product-form');

    // Validate the form.
    if (!validateAddProduct(form)) {
        // return; TODO: Uncomment this.
    }

    // Disable the photo input, so it doesn't get sent.
    $('#photos').prop('disabled', true);

    // Get all the data that we want to send.
    // Form data
    let productData = new FormData(form);

    // Category data
    categoryTree.values.forEach(function (category) {
        productData.append('categories[]', category);
    });

    // Image data
    validatedImages.forEach(function (image) {
        productData.append('images[' + image.id + ']', image.file);
        productData.append('imageOrder[' + image.id + ']', image.order);
    });

    $.ajax({
        type: "POST",
        enctype: 'multipart/form-data',
        url: baseUrl + "admin/product",
        dataType: "json",
        data: productData,
        processData: false,
        contentType: false,
        success: function (data) {
            $("#added-modal").modal('hide');
            $("#submit-btn").prop("disabled", false);

            // Add the new product to the table.
            addToProductsTable(data, true);

            // Close the modal.
            $("#add-product-modal").modal('hide');

            // Reset the form.
            resetForm();
        },
        error: function (e) {
            $("#add-product-error").removeClass('visually-hidden');
            $("#submit-btn").prop("disabled", false);
        }
    });
}

/**
 * Delete a product (request).
 *
 * @param productId
 */
function deleteProduct(productId) {
    $.ajax({
        type: "DELETE",
        url: baseUrl + "admin/product/" + productId,
        dataType: "json",
        success: function (data) {
            // Remove the product from the table.
            removeFromProductsTable(productId);
        },
        error: function (e) {
            console.log(e);
        }
    });
}

/**
 * Update a product (request).
 *
 * @param productId
 */
function updateProduct(productId) {
    // Disable the submit button
    $("#submit-btn").prop("disabled", true);

    // Get the form
    let form = document.getElementById('add-product-form');

    // Validate the form.
    if (!validateAddProduct(form)) {
        return;
    }

    // Disable the photo input, so it doesn't get sent.
    $('#photos').prop('disabled', true);

    // Get all the data that we want to send.
    // Form data
    let productData = new FormData(form);

    // Category data
    categoryTree.values.forEach(function (category) {
        productData.append('categories[]', category);
    });

    // Image data
    validatedImages.forEach(function (image) {
        if (image.type === 'new') {
            productData.append('images[' + image.id + ']', image.file);
        }
        productData.append('imageOrder[' + image.id + ']', image.order);
    });

    // Removed images
    removedImages.forEach(function (imageId) {
        productData.append('removedImages[]', imageId);
    });

    $.ajax({
        type: "POST",
        enctype: 'multipart/form-data',
        url: baseUrl + "admin/product/" + productId + "/update",
        dataType: "json",
        data: productData,
        processData: false,
        contentType: false,
        success: function (data) {
            $("#added-modal").modal('hide');
            $("#submit-btn").prop("disabled", false);

            // Update the product in the table.
            editProductInProductsTable(productId, data);

            // Close the modal.
            $("#add-product-modal").modal('hide');

            // Reset the form.
            resetForm();
        },
        error: function (e) {
            $("#add-product-error").removeClass('visually-hidden');
            $("#submit-btn").prop("disabled", false);
        }
    });
}

/**
 * Reset the form.
 */
function resetForm() {
    let form = document.getElementById('add-product-form');
    form.classList.remove('was-validated');

    // Reset the form.
    form.reset();

    // Reset the category tree.
    resetCategoryTree();

    // Hide the warning
    $("#add-product-error").addClass('visually-hidden');

    // Reset the thumbnail.
    $("#thumbnail-preview").addClass('visually-hidden');

    // Reset the images.
    validatedImages = [];
    removedImages = [];
    imageCount = 0;
    document.getElementById('photo-previews').innerHTML = '';
}

/**
 * Open the add product modal.
 */
function addProductModal() {
    resetForm();

    // Set the submit button to add.
    let submitButton = $("#submit-btn");
    submitButton.attr('onclick', 'addProduct()');
    submitButton.html('Add');

    // Make the thumbnail input required.
    $("#thumbnail").prop('required', true);

    $("#add-product-modal").modal('show');
}

/**
 * Open the edit product modal.
 *
 * @param productId
 */
function editProductModal(productId) {
    // Reset the form.
    resetForm();

    let modal = $("#add-product-modal");

    $.ajax({
        type: "GET",
        url: baseUrl + "admin/product/" + productId,
        dataType: "json",
        success: function (data) {
            let product = data.product;
            let categories = data.categories;
            let images = data.images;

            // Set the values.
            $("#name").val(product.name);
            $("#description").val(product.description);
            $("#price").val(product.price);
            $("#stock").val(product.stock_quantity);
            $("#weight").val(product.weight);
            $("#depth").val(product.depth);
            $("#width").val(product.width);
            $("#height").val(product.height);
            $("#ean13").val(product.ean13);

            // Set the thumbnail.
            let thumbnail = $("#thumbnail-preview");
            thumbnail.attr('src', product.thumbnail_path);
            thumbnail.removeClass('visually-hidden');

            // Make the thumbnail input optional.
            $("#thumbnail").prop('required', false);

            // Set the categories.
            resetCategoryTree();
            for (let category of categories) {
                categoryTree.onItemClick(category.id);
            }

            // Sort the images by their order_index
            images.sort(function (a, b) {
                return a.order_index - b.order_index;
            });

            // Set the images.
            validatedImages = [];
            for (let image of images) {
                validatedImages.push({
                    id: image.id,
                    src: image.image_path,
                    order: image.order_index,
                    type: 'server'
                });
                previewImage(image.image_path, image.id, image.alt);
            }

            // Set the submit button to update.
            let submitButton = $("#submit-btn");
            submitButton.attr('onclick', 'updateProduct(' + productId + ')');
            submitButton.html('Update');

            modal.modal('show');
        },
        error: function (e) {
            console.log(e);
        }
    });
}