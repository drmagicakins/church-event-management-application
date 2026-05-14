<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Model;

class Sermon extends Model
{
    protected static string $table = 'sermons';

    public static function byCategory(int $categoryId, int $limit = 20): array
    {
        return self::where('category_id = ? AND status = "published"', [$categoryId], 'sermon_date DESC LIMIT ' . $limit);
    }
}
