<?php
/**
 * NewsletterController - Subscribe + admin list
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class NewsletterController extends Controller
{
    public function subscribe(): void
    {
        $email = $this->post('email', '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            redirect_with('/', 'danger', 'Please enter a valid email.');
        }

        try {
            $existing = $this->db->fetchOne('SELECT id FROM subscribers WHERE email = ?', [$email]);
            if (!$existing) {
                $this->db->insert('subscribers', [
                    'email'         => $email,
                    'status'        => 'active',
                    'subscribed_at' => date('Y-m-d H:i:s'),
                ]);
            }
        } catch (\Throwable $e) {}

        redirect_with('/', 'success', 'You are now subscribed to the newsletter.');
    }

    public function adminIndex(): void
    {
        try {
            $subscribers = $this->db->fetchAll('SELECT * FROM subscribers ORDER BY subscribed_at DESC LIMIT 500');
        } catch (\Throwable $e) {
            $subscribers = [];
        }
        $this->view('admin.newsletter.index', ['subscribers' => $subscribers]);
    }
}
