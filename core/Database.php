<?php
/**
 * ============================================================================
 * Database - PDO Singleton with prepared-statement helpers
 * ============================================================================
 * Wraps PDO to:
 *   - Enforce prepared statements everywhere (SQL injection prevention)
 *   - Provide a single shared connection (singleton)
 *   - Throw exceptions instead of silent failures
 *   - Support transactions with commit/rollback helpers
 * ============================================================================
 */

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct(array $config)
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['name'],
            $config['charset']
        );

        try {
            $this->pdo = new PDO($dsn, $config['user'], $config['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // true prepared statements
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$config['charset']}'",
            ]);
        } catch (PDOException $e) {
            error_log('DB connection failed: ' . $e->getMessage());
            throw new \RuntimeException('Database connection failed.');
        }
    }

    /** Singleton accessor. */
    public static function getInstance(array $config = []): self
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    /** Raw PDO handle for edge cases. */
    public function pdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Execute an arbitrary SQL statement with bound parameters.
     * Always uses prepared statements.
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /** Fetch all rows. */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /** Fetch one row. */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $row = $this->query($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /** Fetch a single scalar value. */
    public function fetchValue(string $sql, array $params = [])
    {
        return $this->query($sql, $params)->fetchColumn();
    }

    /** INSERT and return the last-insert ID. */
    public function insert(string $table, array $data): int
    {
        $columns = implode(',', array_map(fn($c) => "`$c`", array_keys($data)));
        $holders = implode(',', array_fill(0, count($data), '?'));
        $sql     = "INSERT INTO `$table` ($columns) VALUES ($holders)";
        $this->query($sql, array_values($data));
        return (int)$this->pdo->lastInsertId();
    }

    /** UPDATE and return affected rows. */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $set = implode(',', array_map(fn($c) => "`$c` = ?", array_keys($data)));
        $sql = "UPDATE `$table` SET $set WHERE $where";
        return $this->query($sql, array_merge(array_values($data), $whereParams))->rowCount();
    }

    /** DELETE and return affected rows. */
    public function delete(string $table, string $where, array $params = []): int
    {
        return $this->query("DELETE FROM `$table` WHERE $where", $params)->rowCount();
    }

    /** Begin / commit / rollback helpers for transactions. */
    public function begin(): bool    { return $this->pdo->beginTransaction(); }
    public function commit(): bool   { return $this->pdo->commit(); }
    public function rollback(): bool { return $this->pdo->rollBack(); }
}
