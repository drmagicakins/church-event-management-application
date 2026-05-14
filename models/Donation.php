<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Model;

class Donation extends Model
{
    protected static string $table = 'donations';

    public static function totalPaid(): float
    {
        return (float)self::db()->fetchValue('SELECT COALESCE(SUM(amount), 0) FROM donations WHERE status = "paid"');
    }

    public static function monthlyTotals(int $months = 12): array
    {
        return self::db()->fetchAll(
            'SELECT DATE_FORMAT(created_at, "%Y-%m") AS month, SUM(amount) AS total
             FROM donations WHERE status = "paid" AND created_at >= DATE_SUB(NOW(), INTERVAL ? MONTH)
             GROUP BY month ORDER BY month ASC',
            [$months]
        );
    }
}
