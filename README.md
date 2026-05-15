# Team Praise Official - Ministry Event Management Platform

A complete, production-ready PHP 8 MVC application for managing Team Praise ministry events, sermons, donations, prayer requests, blog, gallery, and newsletter.

---

## ✨ Features

### Public Website

- **Home** - Hero slider, countdown, latest events, testimonials, newsletter
- **About** - Vision, mission, leadership, core values
- **Events** - Upcoming/past events, registration, calendar
- **Sermons** - Audio/video browser, categories, search, downloads
- **Gallery** - Masonry layout, lightbox, category filters
- **Blog** - Rich posts, categories, tags, comments
- **Contact** - Form, Google Maps, WhatsApp, social links
- **Donation** - Bank details, online giving form, payment placeholder
- **Prayer** - Public/private requests, prayer wall, moderation
- **Livestream** - YouTube/Facebook embed, countdown, live chat placeholder

### Admin Dashboard

- **Dashboard** - Metrics, charts (Chart.js), activity logs
- **CMS** - Homepage sections, hero sliders, SEO, social links
- **Events** - Full CRUD, flyer upload, registration management, export
- **Sermons** - Audio/video upload, YouTube embed, categories
- **Blog** - TinyMCE rich editor, draft/publish/schedule, tags
- **Gallery** - Multi-image upload, drag-drop, compression
- **Users** - Multi-admin roles, permissions, activity logs
- **Donations** - Records, donor list, payment logs, export
- **Contacts** - Inbox, reply system, archive
- **Prayer** - Approve/reject, mark answered, moderation
- **Newsletter** - Subscriber list, bulk email campaigns
- **Settings** - SMTP, SEO, logo/favicon, maintenance mode

### Security

- CSRF protection on every form
- SQL injection prevention via prepared statements (PDO)
- XSS protection via output encoding
- Secure file uploads (MIME + extension validation)
- Password hashing with bcrypt
- Session security (regeneration, httponly, secure, samesite)
- Rate limiting on login
- Activity logging and audit trail
- Role-based access control (RBAC)

### Developer Features

- REST API v1 for mobile apps
- PWA manifest and service worker
- Sitemap generator
- Complete MySQL schema with relationships
- Composer-based dependency management
- Clean MVC architecture
- Production-ready for cPanel and VPS

---

## 🚀 Quick Start

### 1. Clone and install

```bash
git clone <repo-url> teampraise
cd teampraise
composer install
cp .env.example .env
```

### 2. Create the database

```bash
mysql -u root -p < database/schema.sql
```

### 3. Configure `.env`

Edit `.env` with your database credentials, SMTP settings, and `APP_URL`.

### 4. Set permissions

```bash
chmod 755 uploads/ logs/
chmod 644 .env
```

### 5. Start the server

```bash
php -S localhost:8000
```

Visit `http://localhost:8000` for the public site, or `http://localhost:8000/admin/login` for the dashboard.

**Default login:** `superadmin` / `Admin@123` (change immediately!)

---

## 📦 Project Structure

```
teampraise/
├── index.php              # Front controller
├── .htaccess              # Apache routing
├── .env                   # Environment (gitignored)
├── composer.json          # PHP dependencies
├── /config                # App configuration
├── /core                  # Framework kernel
│   ├── Database.php       # PDO wrapper
│   ├── Router.php         # URL routing
│   ├── Controller.php     # Base controller
│   ├── Model.php          # Base model
│   ├── Auth.php           # Authentication
│   ├── Csrf.php           # CSRF tokens
│   ├── Mailer.php         # PHPMailer wrapper
│   └── helpers.php        # Global helpers
├── /controllers           # MVC controllers
├── /models                # ActiveRecord models
├── /views                 # PHP templates
├── /api                   # REST API
├── /uploads               # User uploads
├── /logs                  # Application logs
├── /assets                # Public CSS/JS/images
├── /database              # SQL schema + seeds
└── /vendor                # Composer dependencies
```

---

## 🔧 Deployment

### cPanel / Shared Hosting

1. Upload files to `public_html/`
2. Create MySQL database in cPanel
3. Import `database/schema.sql` via phpMyAdmin
4. Configure `.env` with DB credentials
5. Run `composer install --no-dev` (or upload `vendor/` if no SSH)
6. Set permissions: `chmod 755 uploads/ logs/`

### VPS (Nginx + PHP-FPM)

See `INSTALL.md` for complete Nginx configuration and Let's Encrypt setup.

---

## 📚 API Documentation

### Authentication

```
POST /api/v1/auth/login
Body: { "username": "admin", "password": "..." }
Response: { "ok": true, "token": "...", "user": {...} }
```

### Events

```
GET  /api/v1/events
GET  /api/v1/events/{id}
```

### Sermons

```
GET /api/v1/sermons
```

### Blog

```
GET /api/v1/blog-posts
```

### Donations

```
POST /api/v1/donations
Body: { "donor_name": "...", "amount": 50000, "method": "bank_transfer" }
```

### Prayer Requests

```
POST /api/v1/prayer-requests
Body: { "name": "...", "request": "...", "visibility": "private" }
```

---

## 🛡️ Security Checklist

- [ ] Change the default super admin password
- [ ] Generate a unique 64-character `APP_KEY`
- [ ] Set `APP_DEBUG=false` in production
- [ ] Enable HTTPS (Let's Encrypt)
- [ ] Set `/uploads` and `/logs` permissions to `755`
- [ ] Schedule daily database backups
- [ ] Configure fail2ban on VPS
- [ ] Keep Composer dependencies updated

---

## 📝 License

Proprietary - Team Praise Official

---

## 🤝 Support

For questions or issues, contact: `hello@teampraise.org`
