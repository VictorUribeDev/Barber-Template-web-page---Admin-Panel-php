# Installation Guide

This guide covers setting up **Barbería Premium** on a local or production server.

## Prerequisites

| Requirement | Minimum Version |
|-------------|-----------------|
| PHP         | 7.4 (8.x recommended) |
| MySQL       | 5.7 (8.0+ recommended) |
| Web Server  | Apache 2.4+ (Nginx also works) |

### Required PHP Extensions

- `mysqli` — database connectivity
- `json` — AJAX response encoding
- `fileinfo` — file upload validation (recommended)
- `pdo` — optional, for future migration

Verify your extensions:
```bash
php -m | grep -E 'mysqli|json|fileinfo'
```

## Installation Steps

### 1. Clone or Download the Project

```bash
git clone https://github.com/your-username/barberia-premium.git
cd barberia-premium
```

Or download the ZIP archive and extract it.

### 2. Create the Database

Using the MySQL CLI:
```bash
mysql -u root -p
```

Then inside the MySQL prompt:
```sql
CREATE DATABASE barberia
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
EXIT;
```

Or via phpMyAdmin:
1. Open `http://localhost/phpmyadmin`
2. Click **New** in the sidebar
3. Enter `barberia` as the database name
4. Select `utf8mb4_general_ci` as collation
5. Click **Create**

### 3. Import the Schema & Seed Data

Using the MySQL CLI:
```bash
mysql -u root -p barberia < database/init.sql
```

Using phpMyAdmin:
1. Select the `barberia` database
2. Click the **Import** tab
3. Choose `database/init.sql`
4. Click **Go**

This creates all tables and inserts seed data (default admin, sample barbers, services, and reservations).

### 4. Configure Database Credentials

Edit `public/includes/config.php`:

```php
<?php
$servername = "localhost";
$username   = "root";       // Your MySQL username
$password   = "";           // Your MySQL password
$dbname     = "barberia";   // Database name (as created above)

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
```

If your setup uses different credentials, update these values. The same settings apply to `public/includes/db.php` (the alternate connection file).

### 5. Configure Upload Permissions (optional)

The admin panel allows uploading photos to `public/assets/images/publicaciones/`. Ensure the web server can write to this directory:

```bash
chmod 755 public/assets/images/publicaciones/
```

On some systems you may need:
```bash
chmod 775 public/assets/images/publicaciones/
chown www-data:www-data public/assets/images/publicaciones/
```

### 6. Start the Development Server

Using PHP's built-in server:
```bash
# Public site
php -S localhost:8000 -t public/

# Admin panel (separate terminal)
php -S localhost:8001 -t admin/
```

Then open:
- Public site: `http://localhost:8000/index.php`
- Admin login: `http://localhost:8001/login.php`

### Apache Configuration

If using Apache, the project can be served from the document root. The `.htaccess` file at the project root can be customized for URL rewriting:

```apache
# Example .htaccess for public/ directory
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

### Nginx Configuration

For Nginx, a basic config might look like:

```nginx
server {
    listen 80;
    server_name barberia.local;
    root /var/www/barberia3/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

## Verification

After installation:

1. **Public site** — Visit the homepage. You should see the carousel and navigation.
2. **Admin login** — Navigate to `admin/login.php` and log in with:
   - Email: `admin@barberia.com`
   - Password: `admin123`
3. **Admin dashboard** — After login, you should see the dashboard with barber/service tables and message inbox.

## Production Deployment

1. **Set correct file permissions:**
   ```bash
   find . -type f -exec chmod 644 {} \;
   find . -type d -exec chmod 755 {} \;
   ```

2. **Configure the web server** with a real domain and HTTPS (Let's Encrypt recommended).

3. **Update database credentials** in `config.php` for production.

4. **Set the document root** to `public/` for the website and create a separate vhost for `admin/`.

5. **Disable directory listing** in your web server configuration.

6. **Install Composer dependencies** if any are added in the future (none currently required).

## Troubleshooting

### Database Connection Error
- Verify MySQL is running: `sudo service mysql status`
- Check credentials in `public/includes/config.php`
- Confirm the database `barberia` exists

### Styles Not Loading
- Ensure `public/assets/css/style.css` is accessible
- Check file permissions on the `assets/` directory

### Admin Login Not Working
- Confirm the `usuarios` table was imported (check `database/init.sql`)
- Try resetting the password by re-running the seed data
- Clear browser cookies/sessions

### File Upload Failures
- Check that `public/assets/images/publicaciones/` is writable by the web server
- Verify `file_uploads` is enabled in `php.ini`

## Next Steps

- Review the [Architecture Guide](ARCHITECTURE.md) for codebase overview
- See [Contributing](CONTRIBUTING.md) if you want to extend the project
