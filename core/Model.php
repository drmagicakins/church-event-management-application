<?php
/**
 * ============================================================================
 * Model - Base ActiveRecord-style model
 * ============================================================================
 * Every entity model (Event, Sermon, BlogPost, Donation, etc.) extends this.
 * Provides generic CRUD helpers so child models only need to declare their
 * table name and (optionally) override specific behaviours.
 * ============================================================================
 */

declare(strict_types=1);

namespace App\Core;

abstract class Model
{
    /** The database table this model wraps. */
    protected static string $table = '';

    /** Primary key column (default: id). */
    protected static string $primaryKey = 'id';

    protected static function db(): Database
    {
        $config = require ROOT_PATH . '/config/app.php';
        return Database::getInstance($config['database'] ?? []);
    }

    public static function find(int $id): ?array
    {
        $table = static::$table;
        $pk    = static::$primaryKey;
        return static::db()->fetchOne("SELECT * FROM `$table` WHERE `$pk` = ?", [$id]);
    }

    public static function findBy(string $column, $value): ?array
    {
        return static::db()->fetchOne("SELECT * FROM `" . static::$table . "` WHERE `$column` = ?", [$value]);
    }

    public static function all(string $orderBy = 'id DESC', int $limit = 1000, int $offset = 0): array
    {
        $sql = sprintf(
            'SELECT * FROM `%s` ORDER BY %s LIMIT %d OFFSET %d',
            static::$table, $orderBy, $limit, $offset
        );
        return static::db()->fetchAll($sql);
    }

    public static function where(string $where, array $params = [], string $orderBy = 'id DESC'): array
    {
        $sql = sprintf('SELECT * FROM `%s` WHERE %s ORDER BY %s', static::$table, $where, $orderBy);
        return static::db()->fetchAll($sql, $params);
    }

    public static function count(string $where = '1=1', array $params = []): int
    {
        return (int)static::db()->fetchValue(
            'SELECT COUNT(*) FROM `' . static::$table . '` WHERE ' . $where,
            $params
        );
    }

    public static function create(array $data): int
    {
        return static::db()->insert(static::$table, $data);
    }

    public static function updateById(int $id, array $data): int
    {
        $pk = static::$primaryKey;
        return static::db()->update(static::$table, $data, "`$pk` = ?", [$id]);
    }

    public static function deleteById(int $id): int
    {
        $pk = static::$primaryKey;
        return static::db()->delete(static::$table, "`$pk` = ?", [$id]);
    }

    /** Free-text search helper for tables that define FULLTEXT indexes. */
    public static function search(string $query, string $columns, int $limit = 20): array
    {
        $sql = sprintf(
            'SELECT *, MATCH(%s) AGAINST(? IN BOOLEAN MODE) AS relevance
             FROM `%s`
             WHERE MATCH(%s) AGAINST(? IN BOOLEAN MODE)
             ORDER BY relevance DESC LIMIT %d',
            $columns, static::$table, $columns, $limit
        );
        return static::db()->fetchAll($sql, [$query, $query]);
    }
}
