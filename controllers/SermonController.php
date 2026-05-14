<?php
/**
 * ============================================================================
 * SermonController - Public sermon browser + admin CRUD
 * ============================================================================
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Models\Sermon;

class SermonController extends Controller
{
    public function index(): void
    {
        $sermons = Sermon::where('status = "published"', [], 'sermon_date DESC');
        $categories = $this->db->fetchAll('SELECT * FROM sermon_categories ORDER BY name');
        $this->view('frontend.sermons', compact('sermons', 'categories'));
    }

    public function adminIndex(): void
    {
        $sermons = Sermon::all('sermon_date DESC');
        $categories = $this->db->fetchAll('SELECT * FROM sermon_categories ORDER BY name');
        $this->view('admin.sermons.index', ['sermons' => $sermons, 'categories' => $categories, 'csrf' => Csrf::field()]);
    }

    public function store(): void
    {
        $data = [
            'category_id'  => $this->post('category_id') ?: null,
            'title'        => $this->post('title'),
            'slug'         => slugify($this->post('title') . '-' . time()),
            'speaker'      => $this->post('speaker'),
            'sermon_date'  => $this->post('sermon_date'),
            'media_type'   => $this->post('media_type'),
            'media_url'    => $this->post('media_url'),
            'download_url' => $this->post('download_url'),
            'description'  => $this->post('description'),
            'status'       => $this->post('status', 'published'),
        ];

        // If user uploaded a media file instead of pasting a URL
        if (!empty($_FILES['media_file']['name'])) {
            $path = $this->upload($_FILES['media_file'], 'sermons');
            if ($path) $data['media_url'] = $path;
        }

        $id = Sermon::create($data);
        $this->logActivity("Created sermon #{$id}", 'Sermons');
        redirect_with('/admin/sermons', 'success', 'Sermon created.');
    }
}
