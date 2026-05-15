<?php
/**
 * API - Auth Controller
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\Auth;

class AuthController extends Controller
{
    public function login(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?: [];
        $username = trim($input['username'] ?? '');
        $password = $input['password'] ?? '';

        if ($username === '' || $password === '') {
            $this->json(['ok' => false, 'error' => 'Username and password required.'], 400);
        }

        $auth  = new Auth($this->config);
        $admin = $auth->attempt($username, $password);

        if (!$admin) {
            $this->json(['ok' => false, 'error' => 'Invalid credentials.'], 401);
        }

        // In production, issue a signed JWT here
        $this->json([
            'ok'    => true,
            'token' => bin2hex(random_bytes(32)),
            'user'  => [
                'id'   => (int)$admin['id'],
                'name' => $admin['name'],
                'role' => $admin['role'],
            ],
        ]);
    }
}
