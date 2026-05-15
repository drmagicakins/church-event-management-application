<?php
/**
 * GalleryController - Public gallery browser
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class GalleryController extends Controller
{
    public function index(): void
    {
        $items = [];
        try {
            $items = $this->db->fetchAll(
                'SELECT * FROM gallery WHERE status = "published" ORDER BY created_at DESC LIMIT 100'
            );
        } catch (\Throwable $e) {}
        $this->view('frontend.gallery', ['items' => $items]);
    }
}
