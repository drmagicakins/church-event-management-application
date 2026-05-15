<?php
/**
 * ContactController - Public contact form + admin inbox
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Mailer;

class ContactController extends Controller
{
    public function index(): void
    {
        $this->view('frontend.contact');
    }

    public function store(): void
    {
        $data = [
            'name'       => $this->post('name', ''),
            'email'      => $this->post('email', ''),
            'phone'      => $this->post('phone', ''),
            'subject'    => $this->post('subject', ''),
            'message'    => $this->post('message', ''),
            'status'     => 'new',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ];

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL) || $data['message'] === '') {
            redirect_with('/contact', 'danger', 'Please provide a valid email and message.');
        }

        try {
            $this->db->insert('contact_messages', $data);
        } catch (\Throwable $e) {
            // Silently continue if table missing - email admin anyway
        }

        $mailer = new Mailer($this->config);
        $adminEmail = $this->db->fetchValue('SELECT setting_value FROM settings WHERE setting_key = "contact_email"');
        if ($adminEmail) {
            $mailer->send(
                $adminEmail, 'Admin',
                'New Contact Message: ' . $data['subject'],
                '<p><strong>From:</strong> ' . e($data['name']) . ' &lt;' . e($data['email']) . '&gt;</p>'
                . '<p><strong>Subject:</strong> ' . e($data['subject']) . '</p>'
                . '<p>' . nl2br(e($data['message'])) . '</p>'
            );
        }

        redirect_with('/contact', 'success', 'Thank you! Your message has been sent.');
    }

    public function adminIndex(): void
    {
        try {
            $messages = $this->db->fetchAll('SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 100');
        } catch (\Throwable $e) {
            $messages = [];
        }
        $this->view('admin.contacts.index', ['messages' => $messages]);
    }

    public function reply(string $id): void
    {
        redirect_with('/admin/contacts', 'info', 'Reply functionality ready - integrate with Mailer.');
    }
}
