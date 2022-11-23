<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPhoto;
use Core\Database\DB;
use Core\Exceptions\HttpNotFoundException;
use Core\Exceptions\ValidationException;
use Core\Http\Request;
use Core\Http\Response;
use Core\Http\UploadedFile;
use Core\Validation\RuleBuilder as Rule;
use Throwable;

class ProductController
{
    /**
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws Throwable
     */
    public function show(Request $request, int $id): Response
    {
        $product = Product::find($id);
        if ($product === null) {
            throw new HttpNotFoundException('This product does not exist.');
        }

        // Get the product images and sort them by the order column.
        $images = $product->productPhotos();
        usort($images, function ($a, $b) {
            return $a->order_index <=> $b->order_index;
        });

        return view('product', [
            'product' => $product,
            'images' => $images
        ]);
    }

    public function showAdmin(Request $request, int $id): Response
    {
        $product = Product::find($id);
        if ($product === null) {
            throw new HttpNotFoundException('This product does not exist.');
        }

        // Make a valid url for the thumbnail image.
        $product->thumbnail_path = url($product->thumbnail_path);

        // Get the product images and sort them by the order column.
        $images = $product->productPhotos();
        usort($images, function ($a, $b) {
            return $a->order_index <=> $b->order_index;
        });

        // Make valid urls for the product images.
        foreach ($images as $image) {
            $image->image_path = url($image->image_path);
        }

        // Get the product categories.
        $categories = $product->categories();

        return jsonResponse([
            'product' => $product,
            'images' => $images,
            'categories' => $categories
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function search(Request $request): Response
    {
        // TODO: orwhere description
        // TODO: pagination
        $search = $request->input('search');
        $products = Product::where('name', 'LIKE', "%$search%")->get();

        // If only one product is found, redirect to the product page.
        if (count($products) === 1) {
            return redirect('/product/' . $products[0]->id);
        }

        $categories = [];
        foreach ($products as $product) {
            $categories = array_merge($categories, $product->categories());
        }

        return view('browse', [
            'title' => 'Search results for "' . $search . '"',
            'products' => $products,
            'categories' => $categories
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Throwable
     */
    public function indexAdmin(Request $request): Response
    {
        $products = Product::all();
        $categories = Category::all();

        // Change the thumbnail path to the full url.
        foreach ($products as $product) {
            $product->thumbnail_path = url($product->thumbnail_path);
        }

        return view('admin/products', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Throwable
     * @throws ValidationException
     */
    public function index(Request $request): Response
    {
        if ($request->prefersHtml()) {
            return view('browse');
        }

        $request->validate([
            'search' => Rule::new()->nullable(),
            'category' => Rule::new()->nullable()->exists(Category::$table, Category::$primaryKey),
            'price_min' => Rule::new()->nullable()->numeric()->minValue(0),
            'price_max' => Rule::new()->nullable()->numeric()->minValue(0),
            'width_min' => Rule::new()->nullable()->numeric()->minValue(0),
            'width_max' => Rule::new()->nullable()->numeric()->minValue(0),
            'height_min' => Rule::new()->nullable()->numeric()->minValue(0),
            'height_max' => Rule::new()->nullable()->numeric()->minValue(0),
            'depth_min' => Rule::new()->nullable()->numeric()->minValue(0),
            'depth_max' => Rule::new()->nullable()->numeric()->minValue(0),
            'weight_min' => Rule::new()->nullable()->numeric()->minValue(0),
            'weight_max' => Rule::new()->nullable()->numeric()->minValue(0),
            'in_stock' => Rule::new()->nullable()->inArray(['true', 'false']),
            'sort' => Rule::new()->nullable()->inArray(['name', 'price', 'width', 'height', 'depth', 'weight']),
            'sort_direction' => Rule::new()->nullable()->inArray(['asc', 'desc']),
        ]);

        $query = DB::table(Product::$table)->withModel(Product::class);

        // Search
        $search = $request->input('search');
        if ($search !== null) {
            $query = $query->where('name', 'LIKE', "%$search%");
        }

        // Category
        $categoryId = $request->input('category');
        if ($categoryId !== null) {
            $category = Category::find($categoryId);
            $products = $category->products();
            $productIds = [];
            foreach ($products as $product) {
                $productIds[] = $product->id;
            }
            $query = $query->whereIn('id', $productIds);

        }

        // Price
        $priceMin = $request->input('price_min');
        $priceMax = $request->input('price_max');
        if ($priceMin !== null) {
            $query = $query->where('price', '>=', $priceMin);
        }
        if ($priceMax !== null) {
            $query = $query->where('price', '<=', $priceMax);
        }

        // Width
        $widthMin = $request->input('width_min');
        $widthMax = $request->input('width_max');
        if ($widthMin !== null) {
            $query = $query->where('width', '>=', $widthMin);
        }
        if ($widthMax !== null) {
            $query = $query->where('width', '<=', $widthMax);
        }

        // Height
        $heightMin = $request->input('height_min');
        $heightMax = $request->input('height_max');
        if ($heightMin !== null) {
            $query = $query->where('height', '>=', $heightMin);
        }
        if ($heightMax !== null) {
            $query = $query->where('height', '<=', $heightMax);
        }

        // Depth
        $depthMin = $request->input('depth_min');
        $depthMax = $request->input('depth_max');
        if ($depthMin !== null) {
            $query = $query->where('depth', '>=', $depthMin);
        }
        if ($depthMax !== null) {
            $query = $query->where('depth', '<=', $depthMax);
        }

        // Weight
        $weightMin = $request->input('weight_min');
        $weightMax = $request->input('weight_max');
        if ($weightMin !== null) {
            $query = $query->where('weight', '>=', $weightMin);
        }
        if ($weightMax !== null) {
            $query = $query->where('weight', '<=', $weightMax);
        }

        // In stock
        $inStock = $request->input('in_stock');
        if ($inStock === 'true') {
            $query = $query->where('stock_quantity', '>', 0);
        }

        // Sort
        $sort = $request->input('sort');
        $sortDirection = $request->input('sort_direction');
        if ($sort !== null && $sortDirection !== null) {
            $query = $query->orderBy($sort, $sortDirection);
        }

        // Execute query and get products
        $products = $query->get();

        // If there is a category selected, get the subcategories and parent categories.
        if (isset($category)) {
            $categories = $category->subcategories();

            // Get the parent categories to use in the breadcrumbs.
            $parents = [];
            $parent = $category->parent();
            while ($parent !== null) {
                $parents[] = $parent;
                $parent = $parent->parent();
            }
        }

        // The first breadcrumb is the home page.
        $breadcrumbs = [
            [
                'name' => 'Home',
                'url' => url('/'),
            ],
        ];

        // Generate the breadcrumbs from the selected category and its parents, or from the search query.
        if (isset($category)) {
            foreach ($parents as $parent) {
                $breadcrumbs[] = [
                    'name' => $parent->name,
                    'url' => url('/product?category=' . $parent->id),
                    'category_id' => $parent->id,
                ];
            }
            $breadcrumbs[] = [
                'name' => $category->name,
                'url' => url('/product?category=' . $category->id),
                'category_id' => $category->id,
            ];
        } elseif ($search !== null) {
            $breadcrumbs[] = [
                'name' => 'Search: ' . htmlspecialchars($search),
                'url' => url('/product?search=' . htmlspecialchars($search)),
            ];
        } else {
            $breadcrumbs[] = [
                'name' => 'Browse',
                'url' => url('/product'),
            ];
        }

        return jsonResponse([
            'products' => $products,
            'categories' => $categories ?? [],
            'category' => $category ?? null,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws ValidationException
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'name' => Rule::new()->required()->minLength(3)->maxLength(255),
            'price' => Rule::new()->required()->numeric()->minValue(0)->maxDigits(18),
            'description' => Rule::new()->nullable()->maxLength(512),
            'width' => Rule::new()->required()->numeric()->minValue(0)->maxDigits(7),
            'height' => Rule::new()->required()->numeric()->minValue(0)->maxDigits(7),
            'depth' => Rule::new()->required()->numeric()->minValue(0)->maxDigits(7),
            'weight' => Rule::new()->required()->numeric()->minValue(0)->maxDigits(7),
            'stock' => Rule::new()->required()->numeric()->minValue(0)->maxDigits(10),
            'ean13' => Rule::new()->required()->minLength(13)->maxLength(13),
            'categories' => Rule::new()->nullable()->isArray(Rule::new()->numeric()->exists(Category::$table, Category::$primaryKey)),
            'thumbnail' => Rule::new()->required()->file()->image(),
            'images' => Rule::new()->nullable()->isArray(Rule::new()->file()->image()),
            'alt' => Rule::new()->nullable()->isArray(),    // TODO: Validate that the keys match those of the images array.
            'imageOrder' => Rule::new()->nullable()->isArray(),
        ]);

        // Save the thumbnail TODO: resize?
        $thumbnail = $request->file('thumbnail');
        $thumbnail_path = $thumbnail->store('product_photos');

        // Insert the product
        $product = Product::create([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'width' => $request->input('width'),
            'height' => $request->input('height'),
            'depth' => $request->input('depth'),
            'weight' => $request->input('weight'),
            'stock_quantity' => $request->input('stock'),
            'ean13' => $request->input('ean13'),
            'thumbnail_path' => DIRECTORY_SEPARATOR . $thumbnail_path,
        ]);

        // Normalize the image array.
        $photos = [];
        foreach ($request->file('images') as $id=>$image) {
            $photos[] = [
                'path' => $image->store('product_photos'),
                'alt' => $request->input('alt')[$id],
                'order' => $request->input('imageOrder')[$id],
            ];
        }

        // Insert the product photos
        // TODO: multiple insert?
        foreach ($photos as $photo) {
            ProductPhoto::create([
                'product_id' => $product->id,
                'image_path' => DIRECTORY_SEPARATOR . $photo['path'],
                'alt' => $photo['alt'],
                'order_index' => $photo['order'],
            ]);
        }

        // Link the product to the categories
        $product->attachCategories($request->input('categories'));

        // Make a valid url for the product thumbnail.
        $product->thumbnail_path = url($product->thumbnail_path);

        return jsonResponse($product, 201);
    }

    /**
     * @param Request $request
     * @param int $int
     * @return Response
     * @throws ValidationException
     */
    public function update(Request $request, int $int): Response
    {
        $request->validate([
            'name' => Rule::new()->required()->minLength(3)->maxLength(255),
            'price' => Rule::new()->required()->numeric()->minValue(0)->maxDigits(18),
            'description' => Rule::new()->nullable()->maxLength(512),
            'width' => Rule::new()->required()->numeric()->minValue(0)->maxDigits(7),
            'height' => Rule::new()->required()->numeric()->minValue(0)->maxDigits(7),
            'depth' => Rule::new()->required()->numeric()->minValue(0)->maxDigits(7),
            'weight' => Rule::new()->required()->numeric()->minValue(0)->maxDigits(7),
            'stock' => Rule::new()->required()->numeric()->minValue(0)->maxDigits(10),
            'ean13' => Rule::new()->required()->minLength(13)->maxLength(13),
            'categories' => Rule::new()->required()->isArray(Rule::new()->numeric()->exists(Category::$table, Category::$primaryKey)),
            'thumbnail' => Rule::new()->nullable()->file()->image(),
            'images' => Rule::new()->nullable()->isArray(Rule::new()->file()->image()),
            'alt' => Rule::new()->nullable()->isArray(),    // TODO: Validate that the keys match those of the images array.
            'imageOrder' => Rule::new()->nullable()->isArray(),
            'removedImages' => Rule::new()->nullable()->isArray(Rule::new()->numeric()->exists(ProductPhoto::$table, ProductPhoto::$primaryKey)),
        ]);

        // Find the product
        $product = Product::find($int);
        if (is_null($product)) {
            throw new HttpNotFoundException('This product does not exist.');
        }

        // Check if the thumbnail has changed.
        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            // Delete the old thumbnail.
            UploadedFile::delete($product->thumbnail_path);

            // Save the new thumbnail.
            $thumbnail = $request->file('thumbnail');
            $thumbnail_path = $thumbnail->store('product_photos');
            $product->thumbnail_path = DIRECTORY_SEPARATOR . $thumbnail_path;
        }

        // Update the product
        $product->setAttributes([
            'name' => $request->input('name'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
            'width' => $request->input('width'),
            'height' => $request->input('height'),
            'depth' => $request->input('depth'),
            'weight' => $request->input('weight'),
            'stock_quantity' => $request->input('stock'),
            'ean13' => $request->input('ean13'),
        ]);

        $product->save();

        // Normalize the image array.
        $photos = [];
        $requestFiles = $request->file('images') ?? [];
        foreach ($requestFiles as $id=>$image) {
            $photos[] = [
                'path' => $image->store('product_photos'),
                'alt' => $request->input('alt')[$id],
                'order' => $request->input('imageOrder')[$id],
            ];
        }

        // Insert the product photos
        // TODO: multiple insert?
        foreach ($photos as $photo) {
            ProductPhoto::create([
                'product_id' => $product->id,
                'image_path' => DIRECTORY_SEPARATOR . $photo['path'],
                'alt' => $photo['alt'],
                'order_index' => $photo['order'],
            ]);
        }

        // Update the existing product photos
        if (is_array($request->input('imageOrder'))) {
            // Get the existing photos.
            $productPhotos = $product->productPhotos();

            foreach ($request->input('imageOrder') as $id => $order) {
                // Skip new images. (new images will have a non-numeric id (e.g. 'image-1'))
                if (!is_numeric($id)) {
                    continue;
                }

                // Try to find the photo in the productPhotos array.;
                $photo = null;
                foreach ($productPhotos as $productPhoto) {
                    if ($productPhoto->id == $id) {
                        $photo = $productPhoto;
                        break;
                    }
                }

                if (is_null($photo)) {
                    throw new HttpNotFoundException('This product photo does not exist.');
                }

                // Set the new attributes.
                $photo->setAttributes([
                    'alt' => $request->input('alt')[$id],
                    'order_index' => $order,
                ]);

                // Save the photo.
                $photo->save();
            }
        }

        // Delete the removed photos.
        if (is_array($request->input('removedImages'))) {
            foreach ($request->input('removedImages') as $id) {
                // Try to find the photo in the productPhotos array.;
                $photo = null;
                foreach ($productPhotos as $productPhoto) {
                    if ($productPhoto->id == $id) {
                        $photo = $productPhoto;
                        break;
                    }
                }

                if (is_null($photo)) {
                    throw new HttpNotFoundException('This product photo does not exist.');
                }

                // Remove the photo from storage.
                UploadedFile::delete($photo->image_path);

                // Delete the photo from the database.
                $photo->delete();
            }
        }

        // Remove the old categories.
        $product->detachCategories();
        // Add the new categories.
        if (is_array($request->input('categories'))) {
            $product->attachCategories($request->input('categories'));
        }

        // Make a valid url for the product thumbnail.
        $product->thumbnail_path = url($product->thumbnail_path);
        return jsonResponse($product, 200);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function destroy(Request $request, int $id): Response
    {
        $product = Product::find($id);
        if ($product === null) {
            throw new HttpNotFoundException('This product does not exist.');
        }

        $product->delete();

        return jsonResponse(null, 204);
    }
}