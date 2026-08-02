---
name: rbac-auth
description: "Use this skill when touching authentication, roles, permissions, or session handling."
---

# RBAC & Auth Skill

## Purpose

This skill documents the complete role-based access control model, which routes/actions each role may access, and the session/inactivity requirements (FR-1.4).

---

## The 6 Roles

| Role Slug | Display Name | Description |
|-----------|-------------|-------------|
| `admin` | System Administrator | Full system access including user/role management |
| `manager` | Hotel Manager | Reports, room rates, staff oversight |
| `receptionist` | Receptionist / Front Desk | Day-to-day guest and booking operations |
| `housekeeping` | Housekeeping Staff | Room status updates only |
| `accountant` | Accountant / Cashier | Payment recording and invoice management |
| *(guest)* | Hotel Guest | Future portal — not a Spatie role in this version |

---

## Role → Route/Action Access Map

### Admin
- All routes (inherits all permissions below)
- `GET/POST/PUT/DELETE /users` — manage staff accounts
- `GET/POST /users/{user}/roles` — assign/revoke roles
- `DELETE /users/{user}` — delete accounts
- `PATCH /users/{user}/deactivate` — deactivate accounts
- All Manager routes

### Manager
- `GET /dashboard` — full dashboard with revenue
- `GET /reports/occupancy` — occupancy report
- `GET /reports/revenue` — revenue report
- `GET /reports/export/{type}` — PDF/CSV export
- `GET/PUT /room-types` — manage room types and rates (FR-2.1)
- `GET/PUT /rooms` — manage rooms (FR-2.1)
- `GET /guests` — view all guests
- `GET /bookings` — view all bookings
- `PATCH /bookings/{booking}/cancel` — cancel any booking

### Receptionist
- `GET /dashboard` — limited dashboard (occupancy, today's arrivals/departures, no revenue)
- `GET/POST /guests` — register and search guests
- `GET/POST/PUT /bookings` — create, modify, cancel bookings
- `PATCH /bookings/{booking}/checkin` — check-in (FR-5.1)
- `PATCH /bookings/{booking}/checkout` — check-out (FR-5.2)
- `POST /bookings/{booking}/services` — add extra charges
- `GET /rooms` — view room availability (read-only)
- `GET /invoices/{invoice}` — view invoice for a booking

### Housekeeping
- `GET /housekeeping` — view dirty/maintenance rooms assigned to clean
- `PATCH /rooms/{room}/status` — update room status to `available` or `maintenance` (FR-2.3)

### Accountant
- `GET /dashboard` — revenue-focused view (outstanding balances, revenue metrics)
- `GET /invoices` — view all invoices
- `POST /payments` — record payment (FR-6.3)
- `GET /invoices/{invoice}/download` — export invoice PDF (FR-6.4)
- `GET /reports/outstanding` — outstanding balances report (FR-6.5)

---

## Spatie Middleware Setup

In `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role'       => \Spatie\Permission\Middleware\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
    ]);
})
```

Usage in routes:
```php
// Single role
Route::middleware(['auth', 'role:admin'])->group(...);

// Multiple roles (any of)
Route::middleware(['auth', 'role:admin,manager'])->group(...);

// All roles except housekeeping
Route::middleware(['auth', 'role:admin,manager,receptionist,accountant'])->group(...);
```

---

## Demo User Credentials (from UserSeeder)

| Role | Name | Email | Password |
|------|------|-------|----------|
| admin | System Admin | admin@hms.local | password |
| manager | Hotel Manager | manager@hms.local | password |
| receptionist | Front Desk | receptionist@hms.local | password |
| housekeeping | Housekeeper | housekeeping@hms.local | password |
| accountant | Cashier | accountant@hms.local | password |

---

## Session & Auto-Logout (FR-1.4)

### Configuration

`.env`:
```
SESSION_LIFETIME=30
SESSION_DRIVER=database
```

`config/session.php`:
```php
'lifetime'        => env('SESSION_LIFETIME', 30),
'expire_on_close' => true,
```

### Frontend Idle Timer

Included in `resources/views/layouts/app.blade.php` via Alpine.js component. The timer resets on any mouse movement or keypress. After 30 minutes of inactivity, it redirects to `/logout-idle`.

Route:
```php
Route::get('/logout-idle', function () {
    Auth::logout();
    return redirect()->route('login')->with('warning', 'You were logged out due to inactivity.');
})->middleware('auth')->name('logout.idle');
```

---

## User Deactivation (FR-1.3)

When `users.is_active = false`, the user cannot log in. Enforcement:

In `AuthenticatedSessionController::store()` (override Breeze default):

```php
// After credentials are validated:
if (!Auth::user()->is_active) {
    Auth::logout();
    return back()->withErrors([
        'email' => 'Your account has been deactivated. Contact the administrator.',
    ]);
}
```

Or add as a custom validation in the `authenticate()` method.

---

## Role Seeder Reference

```php
// database/seeders/RoleSeeder.php
$roles = ['admin', 'manager', 'receptionist', 'housekeeping', 'accountant'];
foreach ($roles as $role) {
    Role::findOrCreate($role, 'web');
}
```

```php
// database/seeders/UserSeeder.php
$users = [
    ['name' => 'System Admin',    'email' => 'admin@hms.local',        'role' => 'admin'],
    ['name' => 'Hotel Manager',   'email' => 'manager@hms.local',      'role' => 'manager'],
    ['name' => 'Front Desk',      'email' => 'receptionist@hms.local', 'role' => 'receptionist'],
    ['name' => 'Housekeeper',     'email' => 'housekeeping@hms.local', 'role' => 'housekeeping'],
    ['name' => 'Cashier',         'email' => 'accountant@hms.local',   'role' => 'accountant'],
];

foreach ($users as $userData) {
    $user = User::updateOrCreate(
        ['email' => $userData['email']],
        ['name' => $userData['name'], 'password' => Hash::make('password'), 'is_active' => true]
    );
    $user->syncRoles([$userData['role']]);
}
```

---

## Authorization in Blade Views

Use `@can`, `@role`, or `@hasrole` (Spatie provides `@role` directive):

```blade
@role('admin|manager')
    <a href="{{ route('users.index') }}">Manage Users</a>
@endrole

@can('create', App\Models\Booking::class)
    <a href="{{ route('bookings.create') }}">New Booking</a>
@endcan
```

---

## Security Notes

- Never trust Blade conditionals alone for authorization — always enforce at the controller/policy level.
- The `@role` directive is for UX (hiding/showing UI elements), not for security.
- Every controller action that writes data must call `$this->authorize(...)`.
