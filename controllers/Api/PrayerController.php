<?php
/**
 * API - Prayer Request Controller
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;

class PrayerController extends Controller
{
    public function store(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?: [];

        $data = [
            'name'       => trim($input['name'] ?? ''),
            'email'      => trim($input['email'] ?? ''),
            'request'    => trim($input['request'] ?? ''),
            'visibility' => in_array($input['visibility'] ?? '', ['public','private'], true)
                            ? $input['visibility'] : 'private',
            'status'     => 'pending',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ];

        if ($data['name'] === '' || $data['request'] === '') {
            $this->json(['ok' => false, 'error' => 'Name and request are required.'], 422);
        }

        $id = $this->db->insert('prayer_requests', $data);
        $this->json(['ok' => true, 'id' => $id]);
    }
}
