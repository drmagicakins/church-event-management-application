<?php
/**
 * ============================================================================
 * EventController - Public event listing + admin CRUD
 * ============================================================================
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Mailer;
use App\Models\Event;
use App\Models\EventRegistration;

class EventController extends Controller
{
    /** Public events page. */
    public function index(): void
    {
        $upcoming = Event::where('status = "published" AND event_date >= NOW()', [], 'event_date ASC');
        $past     = Event::where('status = "past" OR event_date < NOW()', [], 'event_date DESC');
        $this->view('frontend.events', compact('upcoming', 'past'));
    }

    /** Public single-event page. */
    public function show(string $slug): void
    {
        $event = Event::findBy('slug', $slug);
        if (!$event) {
            http_response_code(404);
            echo '<h1>404 - Event Not Found</h1>';
            return;
        }
        $this->view('frontend.event_show', ['event' => $event]);
    }

    /** Public event registration (AJAX-ready). */
    public function register(string $id): void
    {
        $event = Event::find((int)$id);
        if (!$event || $event['status'] === 'past') {
            $this->json(['ok' => false, 'error' => 'Event unavailable.'], 404);
        }

        $name  = $this->post('full_name', '');
        $email = $this->post('email', '');
        $phone = $this->post('phone', '');
        $ministry = $this->post('ministry', 'Attendee');

        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['ok' => false, 'error' => 'Valid name and email required.'], 422);
        }

        $ticketCode = strtoupper(substr($event['slug'], 0, 3)) . '-' . random_int(1000, 9999);

        EventRegistration::create([
            'event_id'   => $event['id'],
            'full_name'  => $name,
            'email'      => $email,
            'phone'      => $phone,
            'ministry'   => $ministry,
            'ticket_code'=> $ticketCode,
            'status'     => 'confirmed',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        // Send confirmation email (non-blocking)
        $mailer = new Mailer($this->config);
        $mailer->send(
            $email, $name,
            'Registration Confirmed - ' . $event['title'],
            '<h2>You are registered!</h2>'
            . '<p>Event: ' . e($event['title']) . '</p>'
            . '<p>Date: ' . format_date($event['event_date'], 'l, F j, Y g:i A') . '</p>'
            . '<p>Venue: ' . e($event['venue']) . '</p>'
            . '<p><strong>Ticket code: ' . e($ticketCode) . '</strong></p>'
        );

        $this->logActivity("Registered {$email} for event {$event['title']}", 'Events');
        $this->json(['ok' => true, 'ticket_code' => $ticketCode]);
    }

    /** Admin events listing. */
    public function adminIndex(): void
    {
        $events = Event::all('event_date DESC');
        $this->view('admin.events.index', ['events' => $events, 'csrf' => Csrf::field()]);
    }

    /** Admin create event. */
    public function store(): void
    {
        $data = [
            'title'       => $this->post('title'),
            'slug'        => slugify($this->post('title') . '-' . time()),
            'theme'       => $this->post('theme'),
            'description' => $this->post('description'),
            'event_date'  => $this->post('event_date'),
            'venue'       => $this->post('venue'),
            'city'        => $this->post('city'),
            'capacity'    => (int)$this->post('capacity', 0),
            'status'      => $this->post('status', 'draft'),
            'created_by'  => auth_id(),
        ];

        if (!empty($_FILES['flyer']['name'])) {
            $data['flyer'] = $this->upload($_FILES['flyer'], 'events');
        }

        $id = Event::create($data);
        $this->logActivity("Created event #{$id}", 'Events');
        redirect_with('/admin/events', 'success', 'Event created.');
    }

    /** Admin update event. */
    public function update(string $id): void
    {
        $data = [
            'title'       => $this->post('title'),
            'theme'       => $this->post('theme'),
            'description' => $this->post('description'),
            'event_date'  => $this->post('event_date'),
            'venue'       => $this->post('venue'),
            'city'        => $this->post('city'),
            'capacity'    => (int)$this->post('capacity', 0),
            'status'      => $this->post('status'),
        ];

        if (!empty($_FILES['flyer']['name'])) {
            $data['flyer'] = $this->upload($_FILES['flyer'], 'events');
        }

        Event::updateById((int)$id, $data);
        $this->logActivity("Updated event #{$id}", 'Events');
        redirect_with('/admin/events', 'success', 'Event updated.');
    }

    /** Admin delete event. */
    public function destroy(string $id): void
    {
        Event::deleteById((int)$id);
        $this->logActivity("Deleted event #{$id}", 'Events');
        redirect_with('/admin/events', 'success', 'Event deleted.');
    }
}
