# Team Praise Official - Installation Guide

A complete PHP 8 MVC ministry event management platform for Team Praise.

---

## 1. Requirements

- PHP 8.1+ with extensions: `pdo_mysql`, `mbstring`, `openssl`, `json`, `fileinfo`, `gd`
- MySQL 8.0+ (or MariaDB 10.5+)
- Apache 2.4+ with `mod_rewrite` enabled (or Nginx with equivalent rules)
- Composer 2+
- An SMTP account (for PHPMailer)

---

## 2. cPanel / Shared Hosting Deployment

1. **Upload files** to `public_html/` (or a subfolder) via FTP or File Manager.
2. **Create the database** in cPanel → MySQL Databases:
   - Database name: `cpaneluser_teampraise`
   - Database user: `cpaneluser_tpuser`
   - Grant all privileges to the user on the database.
3. **Import the schema**: open phpMyAdmin, select the new database, click *Import*, and upload `database/schema.sql`.
4. **Configure environment**: copy `.env.example` to `.env` and fill in the DB credentials, SMTP details, and `APP_URL`.
5. **Install dependencies** (if SSH is available):
   ```
   cd public_html
   composer install --no-dev --optimize-autoloader
   ```
   If SSH is not available, run `composer install` locally and upload the `vendor/` folder.
6. **Set permissions**:
   ```
   chmod 755 uploads/ logs/
   chmod 644 .env
   ```
7. **Verify `.htaccess`** is in the project root and `mod_rewrite` is enabled.
8. **Browse to** `https://your-domain.com/`. The frontend loads; go to `/admin/login` for the dashboard.

---

## 3. VPS Deployment (Nginx + PHP-FPM)

```
sudo apt update && sudo apt install -y nginx php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-gd php8.1-curl php8.1-xml composer mysql-server
```

Nginx site configuration:

```
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/teampraise;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.(env|git|htaccess) { deny all; }
}
```

Then:
```
git clone <repo> /var/www/teampraise
cd /var/www/teampraise
composer install --no-dev --optimize-autoloader
cp .env.example .env
mysql -u root -p < database/schema.sql
# edit .env with DB credentials + SMTP
sudo systemctl reload nginx php8.1-fpm
```

Enable HTTPS via Certbot:
```
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

---

## 4. Default Admin Login

After importing `database/schema.sql`, sign in at `/admin/login`:

- **Username:** `superadmin`
- **Password:** `Admin@123`

**Change this password immediately** from the admin profile page.

---

## 5. Folder Structure

```
/project-root
├── index.php              # Front controller
├── .htaccess              # Apache routing rules
├── .env                   # Environment (do NOT commit)
├── composer.json          # PHP dependencies
├── /config                # App + DB configuration
├── /core                  # Framework kernel (Database, Router, Auth, etc.)
├── /controllers           # MVC controllers
├── /models                # ActiveRecord-style models
├── /views                 # PHP templates
├── /api                   # REST API entry point
├── /uploads               # User uploads (events, sermons, gallery)
├── /logs                  # Application logs
├── /assets                # Public CSS / JS / images
├── /database              # SQL schema + seeds
├── /vendor                # Composer dependencies (generated)
```

---

## 6. Security Checklist

- [ ] Change the default super admin password.
- [ ] Generate a unique 64-character `APP_KEY` in `.env`.
- [ ] Set `APP_DEBUG=false` in production.
- [ ] Enable HTTPS (Let's Encrypt).
- [ ] Set `/uploads` and `/logs` permissions to `755`.
- [ ] Schedule daily database backups (`mysqldump` via cron).
- [ ] Configure fail2ban on the VPS.
- [ ] Keep Composer dependencies up to date.

---

## 7. Troubleshooting

| Symptom | Fix |
|---|---|
| 500 error | Check `logs/error.log` and file permissions. |
| 404 on all routes | Verify `.htaccess` and that `mod_rewrite` is on. |
| DB connection error | Confirm `.env` credentials and that MySQL is running. |
| Mail not sending | Check SMTP host, port, username, and password in `.env`. |
| Upload fails | Confirm `uploads/` is writable and PHP's `upload_max_filesize` is sufficient. |
