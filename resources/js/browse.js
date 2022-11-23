/**
 * Functions in this file need the variable baseUrl to be set
 */

// Elements
let productContainer = document.getElementById('product-container');
let noProductsFound = document.getElementById('no-products-found');
let productCardTemplate = document.getElementById('product-card');
let title = document.getElementById('title');
let description = document.getElementById('description');
let categorySection = document.getElementById('category-section');
let categoryContainer = document.getElementById('category-container');
let categoryLinkTemplate = document.getElementById('category-link');
let breadcrumbContainer = document.getElementById('breadcrumb-container');
let breadcrumbItemTemplate = document.getElementById('breadcrumb-item');
let breadcrumbItemActiveTemplate = document.getElementById('breadcrumb-item');

// Global variables for filtering and sorting
let selectedCategory = null;
let selectedFilters = {};
let selectedSort = 'name';
let selectedSortDirection = 'asc';

// Get the current url parameters
let urlParams = new URLSearchParams(window.location.search);

// Add a category and search filter from the url if present
if (urlParams.has('category'))
    selectedCategory = urlParams.get('category');
if (urlParams.has('search'))
    selectedFilters.search = urlParams.get('search');

// Request the products from the backend with the given filters and sort
getProducts();

// Intercept the search form submit event, so the page doesn't need to reload
document.getElementById('search-form').addEventListener('submit', function (event) {
    // Prevent the form from submitting
    event.preventDefault();

    // Clear the selected category and filters
    selectedCategory = null;
    clearFilters();

    // Set the search query as a filter
    selectedFilters.search = document.getElementById('search').value;

    // Get the products
    getProducts();
});

/**
 * Render the product cards from the given product objects
 *
 * @param products
 * @uses productCardTemplate
 * @uses productContainer
 * @uses noProductsFound
 * @uses baseUrl
 */
function renderProducts(products) {
    productContainer.innerHTML = '';
    for (let product of products) {
        let productCard = productCardTemplate.content.cloneNode(true);
        let image = productCard.querySelector('.card-img-top');
        let name = productCard.querySelector('.product-name');
        let price = productCard.querySelector('.product-price');
        let outOfStock = productCard.querySelector('.out-of-stock-label');

        image.src = baseUrl + product.thumbnail_path;
        image.alt = product.name;
        name.textContent = product.name;
        price.textContent = product.price;

        // Go to the product page when clicking on the card
        productCard.querySelector('.product-card').onclick = function () {
            window.location.href = baseUrl + 'product/' + product.id;
        };

        if (product.stock_quantity === 0) {
            outOfStock.classList.remove('visually-hidden');
        }

        productContainer.appendChild(productCard);
    }
}

/**
 * Render the subcategory selection from the given category objects
 *
 * @param categories
 * @uses categoryLinkTemplate
 * @uses categoryContainer
 * @uses categorySection
 */
function renderCategories(categories) {
    if (categories.length === 0) {
        categorySection.classList.add('visually-hidden');
        return;
    }
    categorySection.classList.remove('visually-hidden');

    categoryContainer.innerHTML = '';
    for (let category of categories) {
        let categoryLink = categoryLinkTemplate.content.cloneNode(true);
        let link = categoryLink.querySelector('.category-link');
        link.textContent = category.name;
        link.onclick = function () {
            applyCategory(category.id);
        };
        categoryContainer.appendChild(categoryLink);
    }
}

/**
 * Render the breadcrumb from the given breadcrumb objects
 *
 * @param breadcrumbs
 * @uses breadcrumbItemTemplate
 * @uses breadcrumbItemActiveTemplate
 * @uses breadcrumbContainer
 */
function renderBreadcrumbs(breadcrumbs) {
    if (breadcrumbs.length === 0) {
        breadcrumbContainer.classList.add('visually-hidden');
        return;
    }

    // Remove all the current breadcrumbs
    breadcrumbContainer.innerHTML = '';

    // Add all breadcrumbs except the last one
    for (let i = 0; i < breadcrumbs.length - 1; i++) {
        let breadcrumbItem = breadcrumbItemTemplate.content.cloneNode(true);
        let link = breadcrumbItem.querySelector('.breadcrumb-item a');
        link.textContent = breadcrumbs[i].name;
        link.href = breadcrumbs[i].url;

        // If the breadcrumb is a category, add an onclick event to apply the category filter instead of going to the url
        if ('category_id' in breadcrumbs[i]) {
            link.onclick = function (event) {
                event.preventDefault();
                applyCategory(breadcrumbs[i].category_id);
            };
        }
        breadcrumbContainer.appendChild(breadcrumbItem);
    }

    // Add the last breadcrumb as active
    let breadcrumbItem = breadcrumbItemActiveTemplate.content.cloneNode(true);
    let item = breadcrumbItem.querySelector('.breadcrumb-item');
    item.textContent = breadcrumbs[breadcrumbs.length - 1].name;
    breadcrumbContainer.appendChild(breadcrumbItem);
}

/**
 * Get the products from the backend with the given filters and sort
 *
 * @uses baseUrl
 * @uses selectedCategory
 * @uses selectedFilters
 * @uses selectedSort
 * @uses selectedSortDirection
 * @uses noProductsFound
 * @uses title
 * @uses description
 * @uses renderProducts
 * @uses renderCategories
 * @uses renderBreadcrumbs
 */
function getProducts() {
    let url = baseUrl + 'product';
    let params = new URLSearchParams();
    let titleText = 'Products';
    let descriptionText = null;

    if (selectedFilters) {
        for (let filter in selectedFilters) {
            params.set(filter, selectedFilters[filter]);
        }
    }

    if (selectedSort) {
        params.set('sort', selectedSort);

        if (selectedSortDirection) {
            params.set('sort_direction', selectedSortDirection);
        } else {
            params.set('sort_direction', 'asc');
        }
    }

    if (selectedCategory) {
        params.set('category', selectedCategory);
    }

    $.ajax({
        url: url + '?' + params,
        type: 'GET',
        dataType: "json",
        success: function (data) {
            // Extract the data we need from the response
            let products = data.products;
            let categories = data.categories;
            let category = data.category;
            let breadcrumbs = data.breadcrumbs;

            // Show the no products found message if needed
            if (products.length === 0) {
                noProductsFound.classList.remove('visually-hidden');
            } else {
                noProductsFound.classList.add('visually-hidden');
            }

            // Render the products, categories and breadcrumbs
            renderProducts(products);
            renderCategories(categories);
            renderBreadcrumbs(breadcrumbs);

            // Set the title and description
            if (category) {
                titleText = category.name;
                descriptionText = category.description;
            } else if (params.has('search')) {
                titleText = 'Search results for "' + params.get('search') + '"';
            }

            title.textContent = titleText;
            if (descriptionText) {
                description.textContent = descriptionText;
                description.classList.remove('visually-hidden');
            } else {
                description.classList.add('visually-hidden');
            }
        },
        error: function (error) {
            console.log(error);
            $('#warning').removeClass('visually-hidden');
        }
    });
}

/**
 * Apply the filters from the filter form
 *
 * @returns {boolean}
 * @uses selectedFilters
 * @uses getProducts
 */
function applyFilters() {
    // Check form validation
    let form = document.getElementById('filter-form');
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return false;
    }

    // Get the filter values from the form
    let filters = {
        'price_min': document.getElementById('price_min').value,
        'price_max': document.getElementById('price_max').value,
        'width_min': document.getElementById('width_min').value,
        'width_max': document.getElementById('width_max').value,
        'height_min': document.getElementById('height_min').value,
        'height_max': document.getElementById('height_max').value,
        'depth_min': document.getElementById('depth_min').value,
        'depth_max': document.getElementById('depth_max').value,
        'weight_min': document.getElementById('weight_min').value,
        'weight_max': document.getElementById('weight_max').value,
        'in_stock': document.getElementById('stock').checked
    };

    // Only set the filters that have a value
    for (let filter in filters) {
        if (filters[filter]) {
            selectedFilters[filter] = filters[filter];
        } else {
            delete selectedFilters[filter];
        }
    }

    // Rerender the products
    getProducts();
}

/**
 * Apply the given category filter
 *
 * @param category
 * @uses selectedCategory
 * @uses getProducts
 */
function applyCategory(category) {
    selectedCategory = category;
    getProducts();
}

/**
 * Apply the given sort and sort direction
 *
 * @uses selectedSort
 * @uses selectedSortDirection
 * @uses getProducts
 */
function applySort() {
    let sortSelector = document.getElementById('sort').value;
    let sort = 'name';
    let sortDirection = 'asc';

    switch (sortSelector) {
        case 'name':
            sort = 'name';
            sortDirection = 'asc';
            break;
        case 'price-asc':
            sort = 'price';
            sortDirection = 'asc';
            break;
        case 'price-desc':
            sort = 'price';
            sortDirection = 'desc';
            break;
    }

    selectedSort = sort;
    selectedSortDirection = sortDirection;
    getProducts();
}

/**
 * Clear the filters and the form.
 * The products wil not be rendered, call getProducts() to do that.
 */
function clearFilters() {
    selectedFilters = {};
    document.getElementById('filter-form').reset();
}