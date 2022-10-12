<?php

namespace Core\Database;

use Exception;
use RuntimeException;
use PDO;

class DB extends PDO
{
    /**
     * This singleton instance.
     *
     * @var self
     */
    private static self $instance;

    /**
     * The database connection configuration.
     *
     * @var array
     */
    private static array $config;


    /**
     * $config needs to be an array with the following keys:
     * - driver
     * - host
     * - port
     * - database
     * - username
     * - password
     * - charset
     * @param array $config
     */
    private function __construct(array $config)
    {
        $dsn = $config['driver'] . ':host=' . $config['host'] . ';port=' . $config['port'] . ';dbname=' . $config['database'] . ';charset=' . $config['charset'];

        try {
            parent::__construct($dsn, $config['username'], $config['password']);
        } catch (Exception $e) {
            throw new RuntimeException('Could not connect to database: ' . $e->getMessage());
        }
    }

    /**
     * Get the singleton instance of the database connection.
     *
     * @return static
     */
    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            if(!isset(self::$config)) {
                throw new RuntimeException('DB config not set');
            }
            self::$instance = new self(self::$config);
        }

        return self::$instance;
    }

    /**
     * Set the PDO config
     *
     * @param array $config
     * @return void
     */
    public static function setConfig(array $config): void
    {
        $neededKeys = ['driver', 'host', 'port', 'database', 'username', 'password', 'charset'];
        foreach ($neededKeys as $key) {
            if (!isset($config[$key])) {
                throw new RuntimeException('DB config is missing key: ' . $key);
            }
        }

        self::$config = $config;
    }

    /**
     * Create a new query builder instance that uses the given table.
     *
     * @param string $table
     * @return QueryBuilder
     */
    public static function table(string $table): QueryBuilder
    {
        return new QueryBuilder(self::getInstance(), $table);
    }
}