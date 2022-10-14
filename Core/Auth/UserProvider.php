<?php

namespace Core\Auth;

use App\Models\User;

class UserProvider
{
    public function __construct()
    {

    }

    public function retrieveById($id): ?UserInterface
    {
        return User::find($id);
    }

    public function retrieveByAuthIdentifier($value): ?UserInterface
    {
        return User::findByAuthIdentifier($value);
    }

    public function getAuthIdentifierName(): string
    {
        return User::getAuthIdentifierName();
    }

    public function validateCredentials(UserInterface $user, array $credentials): bool
    {
        return password_verify($credentials['password'], $user->getAuthPassword());
    }

}