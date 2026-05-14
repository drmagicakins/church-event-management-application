<?php
/**
 * ============================================================================
 * AuthController - Admin login, logout, forgot password
 * ============================================================================
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Mailer;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        $this->view('admin.login', [
            'title' => 'Admin Login',
        ]);
    }

    public function login(): void
    {
        $username = $this->post('username', '');
        $password = $this->post('password', '');

        if ($username === '' || $password === '') {
            redirect_with('/admin/login', 'danger', 'Username and password are required.');
        }

        $auth  = new Auth($this->config);
        $admin = $auth->attempt($username, $password);

        if (!$admin) {
            redirect_with('/admin/login', 'danger', 'Invalid credentials or account locked.');
        }

        $this->redirect('/admin/dashboard');
    }

    public function logout(): void
    {
        (new Auth($this->config))->logout();
        $this->redirect('/admin/login');
    }

    public function forgotForm(): void
    {
        $this->view('admin.forgot', ['title' => 'Forgot Password']);
    }

    public function forgot(): void
    {
        $email = $this->post('email', '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            redirect_with('/admin/forgot', 'danger', 'Enter a valid email address.');
        }

        $auth  = new Auth($this->config);
        $token = $auth->issueResetToken($email);

        if ($token) {
            $resetUrl = url('/admin/reset?token=' . urlencode($token) . '&email=' . urlencode($email));
            $mailer   = new Mailer($this->config);
            $mailer->send(
                $email, '',
                'Password Reset - Team Praise Official',
                '<p>Click the link below to reset your password. It expires in 1 hour.</p>'
                . '<p><a href="' . e($resetUrl) . '">' . e($resetUrl) . '</a></p>'
            );
        }

        // Always show the same message to avoid user enumeration
        redirect_with('/admin/login', 'info', 'If an account matches that email, a reset link has been sent.');
    }
}
