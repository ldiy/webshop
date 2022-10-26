<?php

namespace App\Models;

use Core\Model\Model;

/**
 * Class Category
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $thumbnail_path
 */
class Category extends Model
{
    public static string $table = 'category';
    public static string $primaryKey = 'id';

    /**
     * Get the products that belong to this category.
     *
     * @return array<Product>
     */
    public function products(): array
    {
        return $this->hasManyT;
    }

    /**
     * Get a category by its name.
     *
     * @param string $name
     * @return Category|null
     */
    public static function getByName(string $name): ?Category
    {
        $categories = self::where('name', '=', $name);
        return $categories[0] ?? null;
    }
}