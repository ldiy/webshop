<?php

namespace App\Models;

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
 * @property int $manufacturer_id
 * @property string $ean13
 * @property string $thumbnail_path
 * @property $deleted_at // TODO: soft delete + DATE_FORMAT
 */
class Product extends Model
{
    public static string $table = 'product';
    public static string $primaryKey = 'id';

    public function productPhotos(): array
    {
        return $this->hasMany(ProductPhoto::class, 'product_id');
    }

    public function categories(): array
    {
        return $this->belongsToMany(Category::class, 'category_product', 'product_id', 'category_id');
    }

}