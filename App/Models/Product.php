<?php

namespace App\Models;

use Core\Database\DB;
use Core\Model\Model;

/**
 * Class Product
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property float $price
 * @property int $stock_quantity
 * @property float $width
 * @property float $height
 * @property float $depth
 * @property float $weight
 * @property string $ean13
 * @property string $thumbnail_path
 * @property $deleted_at // TODO: soft delete + DATE_FORMAT
 */
class Product extends Model
{
    public static string $table = 'product';
    public static string $primaryKey = 'id';
    public static bool $softDelete = true;

    /**
     * @return array
     */
    public function productPhotos(): array
    {
        return $this->hasMany(ProductPhoto::class, 'product_id');
    }

    /**
     * @return array
     */
    public function categories(): array
    {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id');
    }

    /**
     * Attach categories to the product.
     *
     * @param array<Category|int>|Category|int $categories Category objects or IDs.
     * @return void
     */
    public function attachCategories(array|Category|int $categories): void
    {
        if (!is_array($categories)) {
            $categories = [$categories];
        }
        // TODO: multiple insert
        foreach ($categories as $category) {
            $categoryId = $category instanceof Category ? $category->id : $category;
            DB::table('category_product')->insert([
                'product_id' => $this->id,
                'category_id' => $categoryId,
            ]);
        }
    }

    /**
     * Detach categories from the product.
     *
     * @param array|Category|int|null $categories Category objects or IDs. If null, all categories will be detached.
     * @return void
     */
    public function detachCategories(array|Category|int|null $categories = null): void
    {
        // If no categories are specified, detach all categories.
        if ($categories === null) {
            DB::table('category_product')->where('product_id', '=', $this->id)->delete();
            return;
        }

        // Make sure $categories is an array
        if (!is_array($categories)) {
            $categories = [$categories];
        }

        // Get category IDs
        $categoryIds = [];
        foreach ($categories as $category) {
            $categoryIds[] = $category instanceof Category ? $category->id : $category;
        }

        // Delete
        DB::table('category_product')->where('product_id', '=', $this->id)->whereIn('category_id', $categoryIds)->delete();
    }
}