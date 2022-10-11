<?php

namespace Core\Session;


class Session
{
    /**
     * Session settings, with default values
     *
     * @var array
     */
    private array $settings = [
        'cookie_name' => 'session',
        'domain' => null,
        'secure' => false,
        'http_only' => true,
        'same_site' => 'lax',
        'lifetime' => 0,
    ];

    public function __construct(array $settings = null)
    {
        if ($settings !== null) {
            $this->settings = array_merge($this->settings, $settings);
        }
    }

    /**
     * Start the session with the given settings
     *
     * @return void
     */
    public function start(): void
    {
        // Set the name of the session cookie
        session_name($this->settings['cookie_name']);

        // Use strict mode as recommended by the PHP documentation
        ini_set('session.use_strict_mode', 1);

        // Use cookies to store the session ID on the client side
        ini_set('session.use_cookies', 1);
        ini_set('session.use_only_cookies', 1);

        // Set the session cookie parameters
        session_set_cookie_params([
            'lifetime' => $this->settings['lifetime'],
            'path' => '/',
            'domain' => $this->settings['domain'],
            'secure' => $this->settings['secure'],
            'httponly' => $this->settings['http_only'],
        ]);

        // Start the session
        session_start();
    }

    /**
     * Set a session variable
     *
     * @param string $key
     * @param $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a value from the session
     *
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Check if a key exists in the session
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a key from the session
     *
     * @param string $key
     * @return void
     */
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * Clear the session data
     *
     * @return void
     */
    public function destroy(): void
    {
        $_SESSION = [];
    }

    /**
     * Get all session data
     *
     * @return array
     */
    public function all(): array
    {
        return $_SESSION;
    }

    /**
     * Check if the session has been started
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * Set the name of the session cookie
     *
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        session_name($name);
    }

    /**
     * Regenerate the session ID
     *
     * @return void
     */
    public function regenerateId(): void
    {
        session_regenerate_id();
    }

}