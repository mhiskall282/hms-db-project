# RBAC & Security — Always On

These rules govern authentication, authorization, and security hardening across the entire HMS application.

---

## Roles (Spatie laravel-permission)

Install: `composer require spatie/laravel-permission`

Register roles in `RoleSeeder.php`. Roles are the source of truth — permissions are derived from roles via middleware.

```php
$roles = ['admin', 'manager', 'receptionist', 'housekeeping', 'accountant'];
foreach ($roles as $role) {
    Role::findOrCreate($role, 'web');
}
```

---

## Route Protection — Role Middleware

Gate every route group at the route level. **Never trust the frontend for authorization.**

```php
// routes/web.php
Route::middleware(['auth', 'role:admin,manager'])->group(function () {
    Route::resource('rooms', RoomController::class);
    Route::resource('reports', ReportController::class);
});

Route::middleware(['auth', 'role:receptionist,admin,manager'])->group(function () {
    Route::resource('bookings', BookingController::class);
    Route::resource('guests', GuestController::class);
});

Route::middleware(['auth', 'role:housekeeping,admin,manager'])->group(function () {
    Route::get('housekeeping', [HousekeepingController::class, 'index']);
    Route::patch('rooms/{room}/status', [HousekeepingController::class, 'updateStatus']);
});

Route::middleware(['auth', 'role:accountant,admin,manager'])->group(function () {
    Route::resource('invoices', InvoiceController::class);
    Route::resource('payments', PaymentController::class);
});
```

Use Spatie's `role` middleware alias registered in `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
    ]);
})
```

---

## Policies

Every model that users interact with must have a Policy:

| Model | Policy | Key Methods |
|-------|--------|------------|
| `Room` | `RoomPolicy` | `create`, `update`, `delete` |
| `Booking` | `BookingPolicy` | `create`, `update`, `cancel`, `checkIn`, `checkOut` |
| `Guest` | `GuestPolicy` | `create`, `update`, `viewHistory` |
| `Invoice` | `InvoicePolicy` | `view`, `create`, `export` |
| `Payment` | `PaymentPolicy` | `create` |
| `User` | `UserPolicy` | `create`, `update`, `deactivate`, `delete` |

Policies are registered automatically via model discovery in Laravel 11.

---

## Session & Auto-Logout (FR-1.4)

### Server-Side Session Lifetime

In `.env`:
```
SESSION_LIFETIME=30
```

In `config/session.php`:
```php
'lifetime' => env('SESSION_LIFETIME', 30),
'expire_on_close' => true,
```

### Frontend Idle Timer (Alpine.js)

Add to the main layout blade (`app.blade.php`):
```html
<div x-data="idleTimer()" x-init="startTimer()" @mousemove="resetTimer()" @keydown="resetTimer()">
    @yield('content')
</div>

<script>
function idleTimer() {
    return {
        timeout: null,
        idleMinutes: 30,
        startTimer() {
            this.resetTimer();
        },
        resetTimer() {
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => {
                window.location.href = '/logout-idle';
            }, this.idleMinutes * 60 * 1000);
        }
    }
}
</script>
```

Add a GET `/logout-idle` route that logs the user out and redirects to login with a flash message.

---

## Password Security

- Passwords are hashed using Laravel's default hasher (`bcrypt` by default, upgradeable to `argon2`).
- **Never log, dump, or echo raw passwords** anywhere in the codebase.
- **Never log guest ID numbers** (national ID / passport) in application logs.
- The `User` model must cast `password` to `hashed`:
  ```php
  protected $casts = ['password' => 'hashed'];
  ```

---

## Login Rate Limiting (FR-1.1)

Laravel Breeze applies rate limiting to login by default via `throttle:login`. Verify this is active:

```php
// routes/auth.php (Breeze generated)
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('throttle:login')
    ->name('login');
```

Configure in `AppServiceProvider.php`:
```php
RateLimiter::for('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->input('email').'|'.$request->ip());
});
```

---

## User Deactivation (FR-1.3)

Active/inactive flag on the `users` table (`is_active` boolean). Add middleware or auth guard:

```php
// In AppServiceProvider boot()
Auth::provider('eloquent', function ($app, array $config) {
    return new EloquentUserProvider($app['hash'], $config['model']);
});
```

Override `credentials()` in the login controller to reject inactive users:
```php
protected function credentials(Request $request): array
{
    return array_merge($request->only($this->username(), 'password'), ['is_active' => true]);
}
```

Or add a global query scope to the User model if using Breeze's default flow.

---

## Guest PII Protection

- Guest `id_number` column should NOT appear in application logs or error messages.
- In API responses or blade templates, mask the ID number: show only last 4 characters.
- Use `$hidden` on the `User` model:
  ```php
  protected $hidden = ['password', 'remember_token'];
  ```
- Guest model should have `id_number` marked as fillable but never exported in bulk.

---

## Security Checklist (Run Before Each Commit)

- [ ] All routes are behind `auth` middleware
- [ ] Role-sensitive routes have `role:` middleware
- [ ] No raw passwords in code, logs, or views
- [ ] Form Requests used for all POST/PUT/PATCH/DELETE routes
- [ ] CSRF protection active (Breeze ensures this by default)
- [ ] Rate limiting on login route
- [ ] `is_active` checked on login
