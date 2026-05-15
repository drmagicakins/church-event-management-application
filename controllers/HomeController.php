<?php
/**
 * HomeController - Public homepage and static pages
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Event;
use App\Models\BlogPost;

class HomeController extends Controller
{
    public function index(): void
    {
        $upcoming = [];
        $posts    = [];

        // Gracefully handle missing DB tables on first run
        try {
            $upcoming = Event::upcoming(3);
            $posts    = BlogPost::published(3);
        } catch (\Throwable $e) {
            // DB not yet set up - continue with empty data
        }

        $this->view('frontend.home', compact('upcoming', 'posts'));
    }

    public function about(): void
    {
        $this->view('frontend.about');
    }

    public function livestream(): void
    {
        $this->view('frontend.livestream');
    }
}
