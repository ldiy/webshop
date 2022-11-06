<?php

namespace Core\Auth;

use Core\Session\Session;

class Auth
{
    private const AUTH_SESSION_KEY  = 'auth_user_id';
    private Session $session;
    protected UserInterface $user;
    protected UserProvider $userProvider;

    public function __construct(Session $session, UserProvider $userProvider)
    {
        $this->session = $session;
        $this->userProvider = $userProvider;
    }

    /**
     * Check if the user is authenticated.
     *
     * @return bool
     */
    public function check(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the currently authenticated user.
     * If the user is not authenticated, null is returned.
     *
     * @return UserInterface|null
     */
    public function user(): ?UserInterface
    {
        // If user is already set, just return it
        if (isset($this->user)) {
            return $this->user;
        }

        // Try to get the user from the session
        $id = $this->session->get(self::AUTH_SESSION_KEY);

        if (!is_null($id)) {
            $user = $this->userProvider->retrieveById($id);
            if (!is_null($user)) {
                $this->user = $user;
                return $user;
            }
        }

        return null;
    }

    /**
     * Log the given user in.
     *
     * @param $user
     * @return void
     */
    public function login($user): void
    {
        $this->session->set(self::AUTH_SESSION_KEY, $user->getAttribute('id'));
        $this->user = $user;
    }

    /**
     * Log the user out.
     *
     * @return void
     */
    public function logout(): void
    {
        $this->session->remove(self::AUTH_SESSION_KEY);
        unset($this->user);
    }

    /**
     * Attempt to authenticate the user with the given credentials.
     *
     * @param array $credentials
     * @return bool
     */
    public function attempt(array $credentials): bool
    {
        $user = $this->userProvider->retrieveByAuthIdentifier($credentials[$this->userProvider->getAuthIdentifierName()]);
        if ($this->hasValidCredentials($user, $credentials)) {
            $this->login($user);
            return true;
        }

        return false;
    }

    /**
     * Check if given credentials are valid for the given user.
     *
     * @param $user
     * @param array $credentials
     * @return bool
     */
    public function hasValidCredentials($user, array $credentials): bool
    {
        return $user !== null && $this->userProvider->validateCredentials($user, $credentials);
    }
}