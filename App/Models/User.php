<?php

namespace App\Models;

use Core\Auth\UserInterface;
use Core\Database\DB;
use Core\Model\Model;

class User extends Model implements UserInterface
{
    static string $table = 'test';
    static string $authIdentifierName = 'username';

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
        $result = DB::table(static::$table)->where(static::$authIdentifierName, '=', $identifier)->first();

        if (empty($result)) {
            return null;
        }

        return new static($result);
    }
}