<?php
/**
 * DashboardController - Admin dashboard
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Donation;

class DashboardController extends Controller
{
    public function index(): void
    {
        $stats = [
            'events'          => $this->safeCount('events'),
            'sermons'         => $this->safeCount('sermons'),
            'blog_posts'      => $this->safeCount('blog_posts'),
            'gallery'         => $this->safeCount('gallery'),
            'subscribers'     => $this->safeCount('subscribers'),
            'donations_total' => $this->safeSum('donations', 'amount', 'status = "paid"'),
        ];

        $activity = $this->db->fetchAll(
            'SELECT l.*, a.name AS actor_name
             FROM activity_logs l
             LEFT JOIN admins a ON a.id = l.admin_id
             ORDER BY l.created_at DESC
             LIMIT 15'
        );

        $donationsChart = [];
        try {
            $donationsChart = Donation::monthlyTotals(12);
        } catch (\Throwable $e) {}

        $this->view('admin.dashboard', compact('stats', 'activity', 'donationsChart'));
    }

    private function safeCount(string $table): int
    {
        try {
            return (int)$this->db->fetchValue("SELECT COUNT(*) FROM `$table`");
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function safeSum(string $table, string $column, string $where = '1=1'): float
    {
        try {
            return (float)$this->db->fetchValue("SELECT COALESCE(SUM(`$column`), 0) FROM `$table` WHERE $where");
        } catch (\Throwable $e) {
            return 0.0;
        }
    }
}
