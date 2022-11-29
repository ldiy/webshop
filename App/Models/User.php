<?php

namespace App\Models;

use Core\Auth\UserInterface;
use Core\Database\DB;
use Core\Model\Model;

/**
 * Class User
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $password
 * @property int $role_id
 */
class User extends Model implements UserInterface
{
    static string $table = 'user';
    static string $authIdentifierName = 'email';
    static bool $softDelete = true;
    static string $softDeleteColumn = 'deleted_at';

    protected array $hidden = [
        'password'
    ];

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier(): mixed
    {
        return $this->getAttribute($this::$authIdentifierName);
    }

    public static function getAuthIdentifierName(): string
    {
        return static::$authIdentifierName;
    }

    /**
     * Get the (hashed)password for the user.
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        return $this->getAttribute('password');
    }

    /**
     * Find a user by their unique identifier.
     * (eg. username, email, etc.)
     *
     * @param $identifier
     * @return UserInterface|null
     */
    public static function findByAuthIdentifier($identifier): ?UserInterface
    {
        $result = DB::table(static::$table)->where(static::$authIdentifierName, '=', $identifier);

        if (static::$softDelete) {
            $result = $result->whereNull(static::$softDeleteColumn);
        }

        $result = $result->first();

        if (empty($result)) {
            return null;
        }

        return new static($result);
    }

    /**
     * Get the role that this user has.
     *
     * @return Role
     */
    public function role(): Role
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get the addresses that this user has.
     *
     * @return Address[]
     */
    public function addresses(): array
    {
        return $this->hasMany(Address::class, 'user_id');
    }

    /**
     * Get the orders that this user has.
     *
     * @return Order[]
     */
    public function orders(): array
    {
        return $this->hasMany(Order::class, 'user_id');
    }
}