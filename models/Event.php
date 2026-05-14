<?php
/**
 * ============================================================================
 * Event model
 * ============================================================================
 */

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Event extends Model
{
    protected static string $table = 'events';

    /** Fetch upcoming published events. */
    public static function upcoming(int $limit = 6): array
    {
        return self::where(
            'status = "published" AND event_date >= NOW()',
            [],
            'event_date ASC LIMIT ' . $limit
        );
    }

    /** Fetch past events. */
    public static function past(int $limit = 6): array
    {
        return self::where(
            'status = "past" OR event_date < NOW()',
            [],
            'event_date DESC LIMIT ' . $limit
        );
    }

    /** Count registrations for an event. */
    public static function registrationCount(int $eventId): int
    {
        return (int)self::db()->fetchValue(
            'SELECT COUNT(*) FROM event_registrations WHERE event_id = ?',
            [$eventId]
        );
    }
}
