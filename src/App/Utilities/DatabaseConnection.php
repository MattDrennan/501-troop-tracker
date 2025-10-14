<?php

declare(strict_types=1);

namespace App\Utilities;

use App\Utilities\Configuration;

/**
 * A simple database wrapper for mysqli to abstract database operations.
 */
class DatabaseConnection
{
    private \mysqli $conn;

    public function __construct(Configuration $config)
    {
        $db_settings = $config->get('db');

        $this->conn = new \mysqli(
            $db_settings['host'],
            $db_settings['user'],
            $db_settings['pass'],
            $db_settings['name']
        );

        if ($this->conn->connect_error) {
            trigger_error('Database connection failed: ' . $this->conn->connect_error, E_USER_ERROR);
        }
    }

    /**
     * Prepares, binds, and executes a query, returning a single object.
     */
    public function fetchOne(string $sql, string $types, array $params): ?object
    {
        $statement = $this->conn->prepare($sql);
        if ($statement === false) {
            // In a real app, this should throw an exception or log an error
            return null;
        }
        $statement->bind_param($types, ...$params);
        $statement->execute();
        return $statement->get_result()->fetch_object();
    }
}