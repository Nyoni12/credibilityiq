# CredibilityIQ — cPanel Deployment Guide

## Prerequisites
- cPanel hosting with PHP 8.2+
- MySQL database
- SSH access or cPanel Terminal
- Composer available (most cPanel hosts have it)

---

## Step 1: Create Subdomain in cPanel

1. Log in to cPanel → **Subdomains**
2. Create: `credibilityiq.piquesquid.net`
3. Document root: `/home/yourusername/credibilityiq.piquesquid.net`

---

## Step 2: Upload the Laravel App

### Option A — Git Clone (Recommended)
```bash
# Via cPanel Terminal or SSH
cd /home/yourusername/credibilityiq.piquesquid.net
git clone https://github.com/Nyoni12/credibilityiq.git .
# This clones into the current directory
```

### Option B — File Manager
- Upload a zip of the `laravel/` folder
- Extract into `/home/yourusername/credibilityiq.piquesquid.net/`

---

## Step 3: Set Document Root to /public

In cPanel → **Subdomains**, edit the subdomain and change document root to:
```
/home/yourusername/credibilityiq.piquesquid.net/public
```

**OR** — if you cannot change the document root, the `.htaccess` at the repo root
will automatically redirect traffic into `/public`. Both approaches work.

---

## Step 4: Create MySQL Database

1. cPanel → **MySQL Databases**
2. Create database: `yourusername_credibilityiq`
3. Create user: `yourusername_ciquser` with a strong password
4. Add user to database with **All Privileges**

---

## Step 5: Configure .env

```bash
# In cPanel Terminal
cd /home/yourusername/credibilityiq.piquesquid.net
cp .env.example .env
nano .env   # or use File Manager to edit
```

Fill in:
```env
APP_NAME="CredibilityIQ"
APP_ENV=production
APP_KEY=          # will be generated in Step 6
APP_DEBUG=false
APP_URL=https://credibilityiq.piquesquid.net

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=yourusername_credibilityiq
DB_USERNAME=yourusername_ciquser
DB_PASSWORD=YourStrongPassword

SESSION_DRIVER=file
SESSION_SECURE_COOKIE=true

SUPERADMIN_EMAIL=admin@credibilityiq.com
SUPERADMIN_PASSWORD=YourSuperAdminPassword!
SUPERADMIN_FIRST_NAME=Super
SUPERADMIN_LAST_NAME=Admin
```

---

## Step 6: Install Dependencies & Run Setup

```bash
cd /home/yourusername/credibilityiq.piquesquid.net

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Create storage symlink
php artisan storage:link

# Create SuperAdmin
php artisan credibilityiq:create-superadmin

# Seed demo data (optional)
php artisan db:seed

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Step 7: SSL Certificate

1. cPanel → **SSL/TLS** → **Let's Encrypt SSL**
2. Select `credibilityiq.piquesquid.net`
3. Click **Issue** — it auto-configures HTTPS

---

## Step 8: Fix Permissions

```bash
chmod -R 755 /home/yourusername/credibilityiq.piquesquid.net
chmod -R 775 /home/yourusername/credibilityiq.piquesquid.net/storage
chmod -R 775 /home/yourusername/credibilityiq.piquesquid.net/bootstrap/cache
```

---

## Step 9: Test

1. Visit `https://credibilityiq.piquesquid.net` — landing page should load
2. Visit `https://credibilityiq.piquesquid.net/login`
3. Log in as SuperAdmin: `admin@credibilityiq.com` / your password

---

## Updating the App

```bash
cd /home/yourusername/credibilityiq.piquesquid.net
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Troubleshooting

| Problem | Fix |
|---------|-----|
| 500 error | Check `storage/logs/laravel.log` |
| Blank page | Set `APP_DEBUG=true` temporarily |
| Session issues | Ensure `storage/framework/sessions/` is writable |
| Image uploads fail | Run `php artisan storage:link` |
| PDF fails | Ensure `barryvdh/laravel-dompdf` is installed via composer |

---

## Default Credentials (after seeding)

| Account | Email | Password |
|---------|-------|----------|
| SuperAdmin | admin@credibilityiq.com | Admin@2025! |
| Demo Admin | demo@acme.com | Demo@2025! |

**Change these immediately after first login!**
