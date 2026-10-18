<?php
namespace App\Services;

use PDO;
use RuntimeException;

/**
 * Database Connection Manager
 * 
 * Provides a singleton PDO connection using configuration from config/database.php.
 * Uses prepared statements with exceptions enabled and emulated prepares disabled.
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    private function __construct()
    {
        $config = require dirname(__DIR__, 2) . '/config/database.php';

        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=%s',
            $config['driver'],
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        try {
            $this->connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (\PDOException $e) {
            // In production, this would log the error. Never expose credentials.
            throw new RuntimeException(
                'Database connection failed. Please check your configuration in config/database.php. ' .
                'Error: ' . $e->getMessage()
            );
        }
    }

    /**
     * Get the singleton database instance.
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get the underlying PDO connection.
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Begin a database transaction.
     */
    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Commit the current transaction.
     */
    public function commit(): bool
    {
        return $this->connection->commit();
    }

    /**
     * Rollback the current transaction.
     */
    public function rollback(): bool
    {
        return $this->connection->rollBack();
    }

    /**
     * Execute a query and return the statement.
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch a single row.
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Fetch all rows.
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Get the last inserted ID.
     */
    public function lastInsertId(): string
    {
        return $this->connection->lastInsertId();
    }

    /**
     * Prevent cloning.
     */
    private function __clone() {}

    /**
     * Reset the instance (useful for testing).
     */
    public static function resetInstance(): void
    {
        self::$instance = null;
    }
}
