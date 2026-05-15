<?php
/**
 * API - Blog Controller
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index(): void
    {
        $posts = BlogPost::published();
        $this->json(['ok' => true, 'data' => $posts]);
    }
}
