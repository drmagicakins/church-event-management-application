<?php
/**
 * API - Donation Controller
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Donation;

class DonationController extends Controller
{
    public function store(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?: [];

        $data = [
            'donor_name'      => trim($input['donor_name'] ?? ''),
            'donor_email'     => trim($input['donor_email'] ?? ''),
            'amount'          => (float)($input['amount'] ?? 0),
            'currency'        => 'NGN',
            'method'          => $input['method'] ?? 'bank_transfer',
            'transaction_ref' => 'API-' . strtoupper(bin2hex(random_bytes(6))),
            'purpose'         => trim($input['purpose'] ?? 'General Giving'),
            'status'          => 'pending',
            'ip_address'      => $_SERVER['REMOTE_ADDR'] ?? null,
        ];

        if ($data['donor_name'] === '' || $data['amount'] <= 0) {
            $this->json(['ok' => false, 'error' => 'Name and positive amount required.'], 422);
        }

        $id = Donation::create($data);
        $this->json([
            'ok'        => true,
            'id'        => $id,
            'reference' => $data['transaction_ref'],
        ]);
    }
}
