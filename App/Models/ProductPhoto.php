<?php

namespace App\Models;

use Core\Model\Model;

/**
 * Class ProductPhoto
 *
 * @property int $id
 * @property string $image_path
 * @property int $product_id
 * @property string $alt
 * @property int $order_index
 */
class ProductPhoto extends Model
{
    public static string $table = 'product_photo';
    public static string $primaryKey = 'id';

    public function product(): ?self
    {
        return $this->belongsTo(Product::class);
    }
}