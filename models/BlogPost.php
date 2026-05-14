<?php
declare(strict_types=1);
namespace App\Models;

use App\Core\Model;

class BlogPost extends Model
{
    protected static string $table = 'blog_posts';

    public static function published(int $limit = 12): array
    {
        return self::where(
            'status = "published" AND published_at <= NOW()',
            [],
            'published_at DESC LIMIT ' . $limit
        );
    }
}
