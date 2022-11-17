<?php

namespace App\Models;

use Core\Database\DB;
use Core\Model\Model;

/**
 * Class Category
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $thumbnail_path
 * @property int $parent_id
 * @property bool $display
 */
class Category extends Model
{
    public static string $table = 'category';
    public static string $primaryKey = 'id';

    /**
     * Get the products that belong to the category and its subcategories recursively.
     *
     * @param bool $searchSubcategories
     * @return array
     */
    public function products(bool $searchSubcategories = true): array
    {
        $products = $this->belongsToMany(Product::class, 'category_product', 'category_id', 'product_id');

        if ($searchSubcategories) {
            // Get the subcategories products recursively.
            $subcategories = $this->hasMany(Category::class, 'parent_id');
            foreach ($subcategories as $subcategory) {
                $products = array_merge($products, $subcategory->products());
            }
        }

        return $products;
    }

    /**
     * Get a category by its name.
     *
     * @param string $name
     * @return Category|null
     */
    public static function getByName(string $name): ?Category
    {
        return self::where('name', '=', $name)->first();
    }

    /**
     * Get the parent category.
     *
     * @return Category|null
     */
    public function parent(): ?Category
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the subcategories.
     *
     * @return array
     */
    public function subcategories(): array
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Attach a product to the category.
     *
     * @param Product|int $product Product object or ID.
     * @return void
     */
    public function attachProduct(Product|int $product): void
    {
        $productId = $product instanceof Product ? $product->id : $product;
        DB::table('category_product')->insert([
            'category_id' => $this->id,
            'product_id' => $productId
        ]);
    }

}