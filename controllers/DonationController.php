<?php
/**
 * ============================================================================
 * DonationController - Public giving form + admin records
 * ============================================================================
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Mailer;
use App\Models\Donation;

class DonationController extends Controller
{
    public function index(): void
    {
        $settings = $this->db->fetchOne('SELECT setting_value FROM settings WHERE setting_key = "bank_details"');
        $bank     = $settings ? json_decode($settings['setting_value'], true) : [];
        $this->view('frontend.donation', ['bank' => $bank]);
    }

    /** Record a donation. In production, this is called by the payment
     *  gateway webhook after confirming the transaction. */
    public function store(): void
    {
        $data = [
            'donor_name'      => $this->post('donor_name'),
            'donor_email'     => $this->post('donor_email'),
            'amount'          => (float)$this->post('amount'),
            'currency'        => 'NGN',
            'method'          => $this->post('method', 'bank_transfer'),
            'transaction_ref' => 'TP-' . strtoupper(bin2hex(random_bytes(6))),
            'purpose'         => $this->post('purpose', 'General Giving'),
            'status'          => $this->post('method') === 'bank_transfer' ? 'pending' : 'paid',
            'ip_address'      => $_SERVER['REMOTE_ADDR'] ?? null,
        ];

        if ($data['amount'] <= 0 || $data['donor_name'] === '') {
            redirect_with('/donation', 'danger', 'Please enter your name and a valid amount.');
        }

        $id = Donation::create($data);
        $this->logActivity("Donation recorded #{$id}", 'Donations');

        // Notify admin of new donation
        $mailer = new Mailer($this->config);
        $adminEmail = $this->db->fetchValue('SELECT setting_value FROM settings WHERE setting_key = "contact_email"');
        if ($adminEmail) {
            $mailer->send(
                $adminEmail, 'Admin',
                'New Donation Received',
                '<p>' . e($data['donor_name']) . ' gave ' . format_money($data['amount']) . ' via ' . e($data['method']) . '.</p>'
                . '<p>Purpose: ' . e($data['purpose']) . '</p>'
            );
        }

        redirect_with('/donation', 'success', 'Thank you for your gift. Reference: ' . $data['transaction_ref']);
    }

    public function adminIndex(): void
    {
        $donations = Donation::all('created_at DESC');
        $totalPaid = (float)$this->db->fetchValue('SELECT SUM(amount) FROM donations WHERE status = "paid"');
        $this->view('admin.donations.index', compact('donations', 'totalPaid'));
    }
}
