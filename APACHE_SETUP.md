# ROI Attendance System &mdash; Apache Setup & Deployment Guide

A step-by-step guide to clone, install, configure, and run the **ROI Attendance Management System** on Apache (XAMPP / Linux / Ubuntu / Windows).

---

## 1. System Requirements
- **PHP**: `^8.2` or higher
- **PHP Extensions**: `pdo_mysql`, `curl`, `mbstring`, `openssl`, `tokenizer`, `xml`, `fileinfo`, `zip`
- **Database**: MySQL 8.0+ / MariaDB 10.4+
- **Composer**: `2.x`
- **Node.js & NPM**: Node 18+ & NPM 9+
- **Apache Web Server**: With `mod_rewrite` enabled

---

## 2. Step-by-Step Installation

### Step 1: Clone the Repository
Navigate to your Apache root directory (e.g. `C:/xampp/htdocs` or `/var/www/html`):

```bash
cd /path/to/apache/htdocs
git clone https://github.com/mahethekiller/roi-attendance.git
cd roi-attendance
```

---

### Step 2: Install PHP & Node Dependencies

```bash
# Install PHP Composer dependencies
composer install

# Install Node frontend dependencies & build production assets
npm install
npm run build
```

---

### Step 3: Configure Environment Variables (`.env`)

Copy the example environment file:
```bash
cp .env.example .env
```

Open `.env` in your text editor and configure your MySQL database and Biometric machine settings:

```env
APP_NAME="ROI Attendance"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost/roi-attendance/public

# MySQL Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=roi_attendance
DB_USERNAME=root
DB_PASSWORD=

# Biometric Machine Sync Configuration
BIOMETRIC_API_URL=http://103.25.129.247/prac1111/practice/practice2/get_today_data_api_new.php
BIOMETRIC_CRON_TOKEN=roi_attendance_secure_sync_2026
```

---

### Step 4: Generate Application Encryption Key

```bash
php artisan key:generate
```

---

### Step 5: Database Setup & Seeders

Make sure MySQL is running and the database `roi_attendance` exists:
```sql
CREATE DATABASE IF NOT EXISTS roi_attendance CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Run migrations and database seeders:
```bash
php artisan migrate:fresh --seed
```

> **Default Super Admin Credentials**:
> - **Email**: `admin@example.com`
> - **Password**: `password`

---

## 3. Apache Web Server Configuration

### Option A: Running via XAMPP Subfolder (Quick Setup)
If running directly under `C:\xampp\htdocs\roi-attendance`:
1. Ensure `public/.htaccess` exists (already included).
2. Open your browser and navigate to:
   ```
   http://localhost/roi-attendance/public
   ```

---

### Option B: Apache VirtualHost Configuration (Recommended)

#### 1. Enable Apache `mod_rewrite`
- **Linux/Ubuntu**: `sudo a2enmod rewrite`
- **XAMPP**: Ensure `LoadModule rewrite_module modules/mod_rewrite.so` is uncommented in `httpd.conf`.

#### 2. VirtualHost Entry

**Windows (XAMPP `apache/conf/extra/httpd-vhosts.conf`):**
```apache
<VirtualHost *:80>
    ServerName attendance.local
    DocumentRoot "C:/xampp/htdocs/roi-attendance/public"

    <Directory "C:/xampp/htdocs/roi-attendance/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog "logs/attendance-error.log"
    CustomLog "logs/attendance-access.log" combined
</VirtualHost>
```

**Linux / Ubuntu (`/etc/apache2/sites-available/roi-attendance.conf`):**
```apache
<VirtualHost *:80>
    ServerName attendance.example.com
    DocumentRoot /var/www/html/roi-attendance/public

    <Directory /var/www/html/roi-attendance/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/roi_attendance_error.log
    CustomLog ${APACHE_LOG_DIR}/roi_attendance_access.log combined
</VirtualHost>
```

Enable the site and restart Apache:
```bash
sudo a2ensite roi-attendance.conf
sudo systemctl restart apache2
```

---

### Step 6: Directory Permissions (Linux/macOS)

Ensure the web server has write access to `storage` and `bootstrap/cache`:

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

---

## 4. Setting up Automated Biometric Sync (Cron Job)

### Option 1: Linux / Crontab
Add the following entry to your server's crontab:
```bash
crontab -e
```
Add:
```cron
# Run biometric attendance sync every 15 minutes
*/15 * * * * cd /var/www/html/roi-attendance && php artisan attendance:sync-biometric >> /dev/null 2>&1
```

### Option 2: External HTTP Cron Webhook
Trigger the secure webhook using `curl` or any external cron service:
```bash
curl "http://attendance.local/cron/sync-attendance?token=roi_attendance_secure_sync_2026"
```

---

## 5. Verification & Testing

Run the automated test suite to ensure everything is configured properly:
```bash
php artisan test
```

Expected result:
```
Tests:    49 passed (148 assertions)
Duration: ~2.5s
```

---

## 6. Default Admin Features & Navigation

Once logged in at `/login` with `admin@example.com` / `password`:
- **Dashboard**: High-level KPI metrics and system status.
- **Employee Directory**: Manage employees, download sample CSV, and bulk import records.
- **Attendance Logs**: Daily punch sheet with **"Sync Biometric Data"** button.
- **Sync History Logs**: Audit trail of every automated and manual machine sync.
- **API Documentation**: Interactive endpoint reference with code snippets and `.txt` export.
- **API Access Tokens**: Issue and revoke personal Bearer tokens.
- **API Traffic Logs**: Request inspector with latency metrics and client IP tracking.
