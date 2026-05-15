# REST API v1

This folder previously held a bundled `v1.php` file. API controllers are
now split into `controllers/Api/*.php` so they follow PSR-4 (one class per
file) and are autoloaded automatically.

## Available endpoints

| Method | Endpoint                     | Purpose                        |
|--------|------------------------------|--------------------------------|
| POST   | `/api/v1/auth/login`         | Sign in and receive a token    |
| GET    | `/api/v1/events`             | List published events          |
| GET    | `/api/v1/events/{id}`        | Get a single event             |
| GET    | `/api/v1/sermons`            | List published sermons         |
| GET    | `/api/v1/blog-posts`         | List published blog posts      |
| POST   | `/api/v1/donations`          | Record a donation              |
| POST   | `/api/v1/prayer-requests`    | Submit a prayer request        |

All JSON requests should send `Content-Type: application/json`.
