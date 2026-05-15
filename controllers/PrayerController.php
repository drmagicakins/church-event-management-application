<?php
/**
 * PrayerController - Prayer request submission and admin moderation
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class PrayerController extends Controller
{
    public function index(): void
    {
        $wall = [];
        try {
            $wall = $this->db->fetchAll(
                'SELECT * FROM prayer_requests
                 WHERE visibility = "public" AND status IN ("approved","answered")
                 ORDER BY created_at DESC LIMIT 50'
            );
        } catch (\Throwable $e) {}
        $this->view('frontend.prayer', ['wall' => $wall]);
    }

    public function store(): void
    {
        $data = [
            'name'       => $this->post('name', ''),
            'email'      => $this->post('email', ''),
            'request'    => $this->post('request', ''),
            'visibility' => $this->post('visibility', 'private'),
            'status'     => 'pending',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ];

        if ($data['name'] === '' || $data['request'] === '') {
            redirect_with('/prayer', 'danger', 'Name and prayer request are required.');
        }

        try {
            $this->db->insert('prayer_requests', $data);
        } catch (\Throwable $e) {}

        redirect_with('/prayer', 'success', 'Your prayer request has been submitted.');
    }

    public function adminIndex(): void
    {
        try {
            $requests = $this->db->fetchAll('SELECT * FROM prayer_requests ORDER BY created_at DESC LIMIT 100');
        } catch (\Throwable $e) {
            $requests = [];
        }
        $this->view('admin.prayers.index', ['requests' => $requests]);
    }
}
