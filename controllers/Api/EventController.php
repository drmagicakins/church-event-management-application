<?php
/**
 * API - Event Controller
 */

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Models\Event;

class EventController extends Controller
{
    public function index(): void
    {
        $events = Event::where('status = "published"', [], 'event_date DESC');
        $this->json(['ok' => true, 'data' => $events]);
    }

    public function show(string $id): void
    {
        $event = Event::find((int)$id);
        if (!$event) {
            $this->json(['ok' => false, 'error' => 'Event not found.'], 404);
        }
        $this->json(['ok' => true, 'data' => $event]);
    }
}
