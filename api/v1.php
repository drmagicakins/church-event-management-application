<?php
/**
 * ============================================================================
 * REST API v1 - JSON endpoints for mobile apps and third-party integrations
 * ============================================================================
 * This file is routed via the main Router (see core/Router.php).
 * All endpoints return JSON and use token-based authentication.
 * ============================================================================
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Event;
use App\Models\Sermon;
use App\Models\BlogPost;
use App\Models\Donation;

class EventController extends Controller
{
    public function index(): void
    {
        $events = Event::where('status = "published"', [], 'event_date DESC');
        $this->json(['ok' => true, 'data' => $events]);
    }

    public function show(string $id): void
    {
        $event = Event::find((int)$id);
        if (!$event) {
            $this->json(['ok' => false, 'error' => 'Event not found'], 404);
        }
        $this->json(['ok' => true, 'data' => $event]);
    }
}

class SermonController extends Controller
{
    public function index(): void
    {
        $sermons = Sermon::where('status = "published"', [], 'sermon_date DESC');
        $this->json(['ok' => true, 'data' => $sermons]);
    }
}

class BlogController extends Controller
{
    public function index(): void
    {
        $posts = BlogPost::published();
        $this->json(['ok' => true, 'data' => $posts]);
    }
}

class DonationController extends Controller
{
    public function store(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $this->json(['ok' => false, 'error' => 'Invalid JSON'], 400);
        }

        $data = [
            'donor_name'      => $input['donor_name'] ?? '',
            'donor_email'     => $input['donor_email'] ?? '',
            'amount'          => (float)($input['amount'] ?? 0),
            'currency'        => 'NGN',
            'method'          => $input['method'] ?? 'bank_transfer',
            'transaction_ref' => 'API-' . strtoupper(bin2hex(random_bytes(6))),
            'purpose'         => $input['purpose'] ?? 'General Giving',
            'status'          => 'pending',
            'ip_address'      => $_SERVER['REMOTE_ADDR'] ?? null,
        ];

        $id = Donation::create($data);
        $this->json(['ok' => true, 'id' => $id, 'reference' => $data['transaction_ref']]);
    }
}

class AuthController extends Controller
{
    public function login(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';

        $auth  = new \App\Core\Auth($this->config);
        $admin = $auth->attempt($username, $password);

        if (!$admin) {
            $this->json(['ok' => false, 'error' => 'Invalid credentials'], 401);
        }

        // In production, issue a JWT or opaque token here
        $this->json([
            'ok'    => true,
            'token' => bin2hex(random_bytes(32)), // placeholder
            'user'  => ['id' => $admin['id'], 'name' => $admin['name'], 'role' => $admin['role']]
        ]);
    }
}

class PrayerController extends Controller
{
    public function store(): void
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $this->json(['ok' => false, 'error' => 'Invalid JSON'], 400);
        }

        $id = $this->db->insert('prayer_requests', [
            'name'       => $input['name'] ?? '',
            'email'      => $input['email'] ?? '',
            'request'    => $input['request'] ?? '',
            'visibility' => $input['visibility'] ?? 'private',
            'status'     => 'pending',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        $this->json(['ok' => true, 'id' => $id]);
    }
}
