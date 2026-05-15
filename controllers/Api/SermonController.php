<?php
/**
 * API - Sermon Controller
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Sermon;

class SermonController extends Controller
{
    public function index(): void
    {
        $sermons = Sermon::where('status = "published"', [], 'sermon_date DESC');
        $this->json(['ok' => true, 'data' => $sermons]);
    }
}
