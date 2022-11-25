<?php

namespace App\Models;

use Core\Database\DB;
use Core\Model\Model;

/**
 * Class Address
 *
 * @property int $id
 * @property int $user_id
 * @property int $address_id
 * @property int $status
 * @property int $total_shipping
 * @property int $total_products
 * @property int $total_tax
 * @property string $paid_at
 * @property string $created_at
 * @property string $updated_at
 */
class Order extends Model
{
    static string $table = 'order';
    static string $primaryKey = 'id';
    static bool $timestamps = true;

    protected static array $statuses = [
        'pending' => 0,
        'paid' => 1,
        'shipped' => 2,
    ];

    /**
     * Get the user that owns the order.
     *
     * @return User|null
     */
    public function user(): ?User
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the shipping address for this order.
     *
     * @return Address|null
     */
    public function address(): ?Address
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function products(): array
    {
        return $this->belongsToMany(Product::class, 'order_product', 'order_id', 'product_id');
    }

    /**
     * Attach products to the order.
     * If the first parameter is an Product object, the unit price will be set to the price of the product.
     *
     * @param int|Product $product
     * @param int $quantity
     * @param float $unit_price
     * @return void
     */
    public function attachProduct(int|Product $product, int $quantity, float $unit_price = 0): void
    {
        if ($product instanceof Product) {
            $productId = $product->id;
            $unit_price = $product->price;
        } else {
            $productId = $product;
        }

        DB::table('order_product')->insert([
            'order_id' => $this->id,
            'product_id' => $productId,
            'quantity' => $quantity,
            'unit_price' => $unit_price,
        ]);
    }

    /**
     * Get the status name for this order.
     *
     * @return string
     */
    public function getStatusName(): string
    {
        return array_search($this->status, self::$statuses);
    }

    /**
     * Update the status of the order.
     * The model will immediately be updated in the database.
     *
     * @param string|int $status
     * @return void
     */
    public function setStatus(string|int $status): void
    {
        if (is_string($status)) {
            $this->status = self::findStatusCode($status);
        } else {
            $this->status = $status;
        }
        $this->save();
    }

    /**
     * Get the total price of the order.
     *
     * @return float
     */
    public function getTotalPrice(): float
    {
        return $this->total_shipping + $this->total_products;
    }

    /**
     * Find a status code by its name.
     *
     * @param string $status
     * @return int
     */
    public static function findStatusCode(string $status): int
    {
        return self::$statuses[$status];
    }

    /**
     * Get all the possible statuses.
     *
     * @return array|int[]
     */
    public static function getStatuses(): array
    {
        return self::$statuses;
    }
}