<?php
/**
 * UserController - Admin user management
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

class UserController extends Controller
{
    public function index(): void
    {
        $users = [];
        $admins = [];
        try {
            $users  = $this->db->fetchAll('SELECT * FROM users ORDER BY created_at DESC LIMIT 200');
            $admins = $this->db->fetchAll('SELECT id,name,email,username,role,status,last_login_at FROM admins ORDER BY name');
        } catch (\Throwable $e) {}
        $this->view('admin.users.index', compact('users', 'admins'));
    }
}
