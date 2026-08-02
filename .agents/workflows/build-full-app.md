# Build Full App Workflow

This workflow builds the complete Hotel Management System from scratch (or an empty git repo). Execute each step in order; do not skip steps. Run the verification command at the end of each step before proceeding.

---

## Prerequisites

Before starting, verify:
- PHP 8.2+ available (`php --version`)
- Composer available (`composer --version`)
- Node.js 18+ available (`node --version`)
- MySQL running and accessible
- Database `hms_db` created: `mysql -u root -e "CREATE DATABASE IF NOT EXISTS hms_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"`

---

## Step 1 — Scaffold Laravel 11 Project

```bash
# In the project root (empty git repo)
composer create-project laravel/laravel . --prefer-dist

# Verify
php artisan --version  # Should show Laravel 11.x
```

Configure `.env`:
```
APP_NAME="Hotel Management System"
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hms_db
DB_USERNAME=root
DB_PASSWORD=
```

---

## Step 2 — Install Breeze + Tailwind + Packages

```bash
# Laravel Breeze (Blade stack)
composer require laravel/breeze --dev
php artisan breeze:install blade --dark

# Spatie laravel-permission
composer require spatie/laravel-permission

# PDF + Excel export
composer require barryvdh/laravel-dompdf
composer require maatwebsite/excel

# Alpine.js is already included via Breeze

# Install Node deps and build
npm install
npm run build
```

Publish Spatie config:
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

Add `HasRoles` trait to `User` model:
```php
use Spatie\Permission\Traits\HasRoles;
class User extends Authenticatable {
    use HasRoles;
}
```

**Verify:** `php artisan route:list` shows auth routes.

---

## Step 3 — Run Initial Migration

```bash
php artisan migrate
```

**Verify:** `php artisan migrate:status` shows all migrations as `Ran`.

---

## Step 4 — Build Migrations + Models + Factories + Seeders

Create migrations in dependency order:
1. `room_types`
2. `rooms` (FK → room_types)
3. `guests`
4. `bookings` (FK → guests, rooms, users)
5. `check_in_outs` (FK → bookings, users)
6. `invoices` (FK → bookings)
7. `payments` (FK → invoices, users)
8. `additional_services` (FK → bookings, invoices, users)
9. Add `is_active` column to `users` table

```bash
php artisan make:migration create_room_types_table
php artisan make:migration create_rooms_table
php artisan make:migration create_guests_table
php artisan make:migration create_bookings_table
php artisan make:migration create_check_in_outs_table
php artisan make:migration create_invoices_table
php artisan make:migration create_payments_table
php artisan make:migration create_additional_services_table
php artisan make:migration add_is_active_to_users_table

# Models
php artisan make:model RoomType -f
php artisan make:model Room -f
php artisan make:model Guest -f
php artisan make:model Booking -f
php artisan make:model CheckInOut -f
php artisan make:model Invoice -f
php artisan make:model Payment -f
php artisan make:model AdditionalService -f

# Seeders
php artisan make:seeder RoleSeeder
php artisan make:seeder UserSeeder
php artisan make:seeder RoomTypeSeeder
php artisan make:seeder RoomSeeder
php artisan make:seeder GuestSeeder
php artisan make:seeder BookingSeeder
```

Run full migration with seed:
```bash
php artisan migrate:fresh --seed
```

**Verify:** Check each table exists with `php artisan tinker --execute="echo implode(', ', array_column(DB::select('SHOW TABLES'), 'Tables_in_hms_db'));"`

---

## Step 5 — Auth + RBAC Middleware + Role Seeding

In `RoleSeeder`: create roles (admin, manager, receptionist, housekeeping, accountant).
In `UserSeeder`: create one demo user per role with known credentials.

Add Spatie middleware aliases to `bootstrap/app.php`.

Test:
```bash
php artisan test --filter FR11Test
php artisan test --filter FR12Test
```

---

## Step 6 — Room & RoomType Management (FR-2.x)

```bash
php artisan make:controller RoomTypeController --resource
php artisan make:controller RoomController --resource
php artisan make:request CreateRoomRequest
php artisan make:request UpdateRoomRequest
php artisan make:policy RoomPolicy --model=Room
```

Create `app/Actions/UpdateRoomStatusAction.php` for FR-2.3.

Views: `resources/views/rooms/index.blade.php`, `create.blade.php`, `edit.blade.php`.

Tests:
```bash
php artisan test --filter FR21Test
php artisan test --filter FR22Test
php artisan test --filter FR23Test
```

---

## Step 7 — Guest Management (FR-3.x)

```bash
php artisan make:controller GuestController --resource
php artisan make:request CreateGuestRequest
php artisan make:policy GuestPolicy --model=Guest
```

Views: `resources/views/guests/index.blade.php`, `create.blade.php`, `show.blade.php` (booking history).

Tests:
```bash
php artisan test --filter FR31Test
php artisan test --filter FR32Test
php artisan test --filter FR33Test
```

---

## Step 8 — Booking & Availability (FR-4.x)

```bash
php artisan make:controller BookingController --resource
php artisan make:service AvailabilityService
php artisan make:action CreateBookingAction
php artisan make:request CreateBookingRequest
php artisan make:request UpdateBookingRequest
php artisan make:policy BookingPolicy --model=Booking
php artisan make:notification BookingConfirmed
```

Key: `AvailabilityService::getAvailableRooms($from, $to, $roomTypeId)`.
Key: `CreateBookingAction::execute()` — wrapped in `DB::transaction()` with `lockForUpdate()`.

Tests:
```bash
php artisan test --filter FR41Test   # availability search
php artisan test --filter FR42Test   # create booking
php artisan test --filter FR43Test   # modify/cancel
php artisan test --filter FR44Test   # unique reference
php artisan test --filter FR24Test   # double-booking prevention
```

---

## Step 9 — Check-In / Check-Out (FR-5.x)

```bash
php artisan make:controller CheckInOutController
php artisan make:action CheckInAction
php artisan make:action CheckOutAction
```

Tests:
```bash
php artisan test --filter FR51Test
php artisan test --filter FR52Test
php artisan test --filter FR53Test
```

---

## Step 10 — Billing, Additional Services, Payments (FR-6.x)

```bash
php artisan make:controller InvoiceController --resource
php artisan make:controller PaymentController --resource
php artisan make:controller AdditionalServiceController
php artisan make:service BillingService
php artisan make:action RecordPaymentAction
php artisan make:policy InvoicePolicy --model=Invoice
```

`BillingService::calculateTotal($booking)`: rate × nights + additional services + tax.

Tests:
```bash
php artisan test --filter FR61Test
php artisan test --filter FR62Test
php artisan test --filter FR63Test
php artisan test --filter FR64Test
php artisan test --filter FR65Test
```

---

## Step 11 — Reporting Dashboard (FR-7.x)

```bash
php artisan make:controller DashboardController
php artisan make:controller ReportController
php artisan make:service ReportingService
```

`ReportingService`: occupancy rate, revenue totals, today's check-ins/outs.

PDF export: `ReportController::exportPdf()` → `barryvdh/laravel-dompdf`.
CSV export: `ReportController::exportCsv()` → `maatwebsite/excel`.

Tests:
```bash
php artisan test --filter FR71Test
php artisan test --filter FR72Test
php artisan test --filter FR73Test
php artisan test --filter FR74Test
```

---

## Step 12 — Apply Branding Across All Blade Views

Reference `ui-branding.md` for the full specification:
1. Create `resources/views/layouts/app.blade.php` with sidebar layout.
2. Create `resources/views/components/status-badge.blade.php`.
3. Create `resources/views/components/metric-card.blade.php`.
4. Update `tailwind.config.js` with the Npontu color palette.
5. Update `resources/css/app.css` with CSS variables and Inter font.
6. Apply consistent nav, tables, buttons, and forms across all views.

---

## Step 13 — Run Full Test Suite

```bash
php artisan test --verbose
```

All tests must pass (green). Fix failures before proceeding to Step 14.

---

## Step 14 — Demo Seed & Final Documentation

```bash
php artisan migrate:fresh --seed
```

Update `PROGRESS.md` to show all FR IDs ✅.

Update `README.md` with:
- Setup instructions (clone, `.env`, `composer install`, `npm install`, `php artisan migrate:fresh --seed`, `php artisan serve`)
- Demo credentials for all 6 roles
- Brief feature overview

---

## Step 15 — Commit & Tag

```bash
git add -A
git commit -m "feat: complete HMS application — all FR-1 through FR-7 implemented and tested"
git tag -a v1.0.0 -m "Final Year Project submission"
```
