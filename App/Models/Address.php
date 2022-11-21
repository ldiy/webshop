<?php

namespace App\Models;

use Core\Model\Model;

/**
 * Class Address
 *
 * @property int $id
 * @property int $user_id
 * @property string $country_code
 * @property string $postcode
 * @property string $city
 * @property string $address_line1
 * @property string $address_line2
 * @property string $first_name
 * @property string $last_name
 */
class Address extends Model
{
    static string $table = 'address';
    static string $primaryKey = 'id';

    /**
     * Available countries.
     * For now, these are hardcoded.
     *
     * @var array|string[] [country_code => country_name]
     */
    private static array $countries = [
        'BE' => 'Belgium',
        'NL' => 'Netherlands',
        'DE' => 'Germany',
        'FR' => 'France',
        'UK' => 'United Kingdom',
    ];

    /**
     * Get the user that owns the address.
     *
     * @return User|null
     */
    public function user(): ?User
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the country name for this address.
     *
     * @return string
     */
    public function getCountryName(): string
    {
        return self::$countries[$this->country_code];
    }

    /**
     * Get a list of all available countries.
     *
     * @return array|string[]
     */
    public static function getAvailableCountries(): array
    {
        return self::$countries;
    }
}