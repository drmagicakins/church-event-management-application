<?php
/**
 * SettingsController - Admin site settings
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;

class SettingsController extends Controller
{
    public function index(): void
    {
        $rows = [];
        try {
            $rows = $this->db->fetchAll('SELECT * FROM settings ORDER BY setting_key');
        } catch (\Throwable $e) {}
        $settings = [];
        foreach ($rows as $r) $settings[$r['setting_key']] = $r['setting_value'];
        $this->view('admin.settings.index', ['settings' => $settings, 'csrf' => Csrf::field()]);
    }

    public function update(): void
    {
        foreach (['site_name','tagline','contact_email','phone','address','seo_title','seo_description'] as $key) {
            $value = $this->post($key);
            if ($value === null) continue;
            try {
                $this->db->query(
                    'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
                     ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)',
                    [$key, $value]
                );
            } catch (\Throwable $e) {}
        }
        $this->logActivity('Settings updated', 'Settings');
        redirect_with('/admin/settings', 'success', 'Settings saved.');
    }
}
