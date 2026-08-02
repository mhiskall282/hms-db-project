# Deploy Local Workflow

Use this workflow to get the HMS app running locally for demo or development.

---

## Prerequisites

- XAMPP (or equivalent) running with MySQL
- PHP 8.2+ in PATH (or use `C:\xampp\php\php.exe` prefix)
- Composer available
- Node.js 18+
- Database `hms_db` exists in MySQL

---

## Step 1 — Install/Update Dependencies

```bash
composer install --no-interaction --prefer-dist
npm install
```

---

## Step 2 — Configure Environment

Ensure `.env` exists and has:
```
APP_NAME="Hotel Management System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hms_db
DB_USERNAME=root
DB_PASSWORD=

SESSION_LIFETIME=30
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

Generate app key if not set:
```bash
php artisan key:generate
```

---

## Step 3 — Fresh Database with Demo Data

```bash
php artisan migrate:fresh --seed
```

This will:
1. Drop all tables and re-run migrations
2. Run `DatabaseSeeder` which calls all module seeders
3. Create demo users for all 6 roles
4. Create sample rooms, room types, guests, and bookings
5. Include a booking that demonstrates anti-double-booking enforcement

---

## Step 4 — Build Frontend Assets

```bash
npm run build
```

For development with hot-reload:
```bash
npm run dev
```

---

## Step 5 — Start the Server

```bash
php artisan serve
```

App will be available at: **http://localhost:8000**

---

## Step 6 — Demo Login Credentials

| Role | Email | Password |
|------|-------|----------|
| System Administrator | admin@hms.local | password |
| Hotel Manager | manager@hms.local | password |
| Receptionist | receptionist@hms.local | password |
| Housekeeping | housekeeping@hms.local | password |
| Accountant | accountant@hms.local | password |

> **Note:** All demo passwords are `password`. Change before any production deployment.

---

## Step 7 — Verify the App

After logging in with each role, verify:
- [ ] Admin: Can manage users (create, deactivate, assign roles)
- [ ] Manager: Can view dashboard and all reports
- [ ] Receptionist: Can register guest, create booking, check-in/out
- [ ] Housekeeping: Can see dirty rooms and update status to clean
- [ ] Accountant: Can record payment, generate invoice PDF

---

## Troubleshooting

### `php artisan serve` port already in use
```bash
php artisan serve --port=8080
```

### Database connection error
1. Ensure XAMPP MySQL is running
2. Check `DB_HOST`, `DB_PORT`, `DB_USERNAME`, `DB_PASSWORD` in `.env`
3. Verify database exists: `mysql -u root -e "SHOW DATABASES;"`

### Assets not loading (CSS/JS 404)
```bash
npm run build
# Then hard-refresh browser (Ctrl+Shift+R)
```

### Permission errors on Spatie roles
```bash
php artisan cache:clear
php artisan permission:cache-reset
```

### Session errors after migration
```bash
php artisan session:table
php artisan migrate
```
