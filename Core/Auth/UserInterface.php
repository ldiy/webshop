<?php

namespace Core\Auth;

use Core\Model\Model;

interface UserInterface
{
    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier(): mixed;

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(): string;

    /**
     * Find a user by their unique identifier.
     * (eg. username, email, etc.)
     *
     * @param $identifier
     * @return UserInterface|null
     */
    public static function findByAuthIdentifier($identifier): ?UserInterface;

    public static function getAuthIdentifierName(): string;

}