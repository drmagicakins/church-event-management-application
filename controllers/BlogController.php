<?php
/**
 * BlogController - Public blog + admin CRUD
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index(): void
    {
        $posts = [];
        try { $posts = BlogPost::published(); } catch (\Throwable $e) {}
        $this->view('frontend.blog', ['posts' => $posts]);
    }

    public function show(string $slug): void
    {
        $post = BlogPost::findBy('slug', $slug);
        if (!$post) { http_response_code(404); echo '<h1>404</h1>'; return; }
        $this->view('frontend.blog_show', ['post' => $post]);
    }

    public function adminIndex(): void
    {
        $posts = [];
        try { $posts = BlogPost::all('published_at DESC'); } catch (\Throwable $e) {}
        $this->view('admin.blog.index', ['posts' => $posts, 'csrf' => Csrf::field()]);
    }

    public function store(): void
    {
        redirect_with('/admin/blog', 'info', 'Blog editor ready - integrate TinyMCE.');
    }
}
