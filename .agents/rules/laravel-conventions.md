# Laravel Conventions — Always On

These conventions apply to every file in this Laravel 11 project. All agents and contributors must follow them without exception.

---

## Application Structure

### Laravel 11 Conventions
- Single `AppServiceProvider` — do not create multiple service providers unless absolutely required.
- Route files: `routes/web.php` for browser routes, `routes/api.php` for API routes. No business logic in route files.
- Controller registration: use `Route::resource()` or `Route::get/post/put/delete()` with named routes.
- Use `php artisan route:list` to verify routes exist after each module build.

### Directory Layout (under `app/`)
```
app/
├── Actions/              ← Single-purpose action classes (e.g. CreateBookingAction.php)
├── Services/             ← Stateful service classes (e.g. AvailabilityService, BillingService)
├── Http/
│   ├── Controllers/      ← Thin — delegate to Actions/Services
│   ├── Requests/         ← Form Request classes for ALL validation
│   └── Middleware/       ← Custom middleware (e.g. CheckRole, InactivityTimeout)
├── Models/               ← Eloquent models only
├── Policies/             ← Gate/Policy classes per model
└── Notifications/        ← Laravel notifications (BookingConfirmed, etc.)
```

---

## Controllers Must Stay Thin

Controllers may:
- Validate input via Form Request injection
- Call one Action or Service method
- Return a view or redirect

Controllers must NOT:
- Contain business logic
- Perform database queries directly (use Eloquent through models/actions)
- Perform calculations (delegate to BillingService)

**Bad:**
```php
public function store(Request $request) {
    $nights = Carbon::parse($request->check_out)->diffInDays($request->check_in);
    $total = $nights * Room::find($request->room_id)->roomType->base_rate;
    // ... 40 more lines
}
```

**Good:**
```php
public function store(CreateBookingRequest $request, CreateBookingAction $action) {
    $booking = $action->execute($request->validated());
    return redirect()->route('bookings.show', $booking)->with('success', 'Booking created.');
}
```

---

## Form Requests

- Every controller method that writes data must use a dedicated Form Request class.
- Validation rules go in `rules()`, authorization in `authorize()`.
- Use `prepareForValidation()` to sanitize/normalize inputs before validation.

```php
class CreateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Booking::class);
    }

    public function rules(): array
    {
        return [
            'guest_id'       => ['required', 'exists:guests,id'],
            'room_id'        => ['required', 'exists:rooms,id'],
            'check_in_date'  => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
        ];
    }
}
```

---

## Eloquent Relationships & Query Scopes

Use Eloquent relationships — never join with raw SQL unless performance profiling demands it.

```php
// Room model — availability scope
public function scopeAvailable(Builder $query, Carbon $from, Carbon $to): Builder
{
    return $query->where('status', '!=', RoomStatus::Maintenance)
        ->whereDoesntHave('bookings', function (Builder $q) use ($from, $to) {
            $q->whereNotIn('status', ['cancelled'])
              ->where('check_in_date', '<', $to)
              ->where('check_out_date', '>', $from);
        });
}
```

Scope naming convention: `scope{Adjective}` — e.g. `scopeAvailable`, `scopeOverlapping`, `scopeUnpaid`.

---

## Database Migrations

Rules:
1. Every migration must have a corresponding `down()` that fully reverses it.
2. Use `foreignId('column_id')->constrained()->cascadeOnDelete()` for FKs.
3. Add indexes on: FK columns, status columns used in WHERE clauses, `booking_reference`, `room_number`, `email`.
4. Soft deletes (`SoftDeletes` trait + `deleted_at` column) on: `guests`, `bookings`, `users`.
5. Use `unsignedBigInteger` or `foreignId` for all FK columns.

### Anti-Double-Booking — DB-Level Enforcement

> **Never rely solely on a PHP pre-check.** Race conditions under concurrent requests will slip through.

Enforce with a database transaction + pessimistic locking:

```php
DB::transaction(function () use ($data) {
    // Lock the room row to prevent concurrent overlapping bookings
    $room = Room::lockForUpdate()->findOrFail($data['room_id']);

    $overlap = Booking::where('room_id', $data['room_id'])
        ->whereNotIn('status', ['cancelled'])
        ->where('check_in_date', '<', $data['check_out_date'])
        ->where('check_out_date', '>', $data['check_in_date'])
        ->exists();

    if ($overlap) {
        throw new RoomNotAvailableException('Room is already booked for the selected dates.');
    }

    return Booking::create($data);
});
```

---

## Factories & Seeders

Every model must have:
1. A `ModelFactory` using `Faker` with realistic data.
2. A seeder class under `database/seeders/`.
3. The `DatabaseSeeder` must call all seeders in dependency order.

Demo seed requirements:
- At least 3 `RoomType` records (Single, Double, Suite).
- At least 10 `Room` records spread across types.
- At least 5 `Guest` records.
- At least 3 `Booking` records — one of which is a deliberate overlap that the system must reject in a test.
- One demo `User` per role (admin, manager, receptionist, housekeeping, accountant).

---

## Wrapping Critical Operations in Transactions

The following operations MUST be wrapped in `DB::transaction()`:
1. `CreateBookingAction` — booking creation with overlap check.
2. `RecordPaymentAction` — payment recording with invoice balance update.
3. `CheckInAction` — check-in with room status update.
4. `CheckOutAction` — check-out with room status update and invoice generation trigger.
5. Any bulk-update operation (e.g. mass room status update).

---

## Testing Standards

- Use **Pest** as the test runner.
- One feature test file per FR ID, named `FR{N}{M}Test.php` (e.g. `FR41Test.php` for FR-4.1).
- Tests live in `tests/Feature/`.
- Every test must:
  - Seed minimal required data (use factories, not `DatabaseSeeder`).
  - Test the happy path AND at least one error case.
  - Assert both the HTTP response AND the database state.

```php
// Example: FR-4.4 — unique booking reference
it('generates a unique booking reference for each booking', function () {
    $receptionist = User::factory()->create()->assignRole('receptionist');
    $guest = Guest::factory()->create();
    $room = Room::factory()->available()->create();

    $response = $this->actingAs($receptionist)
        ->post('/bookings', [
            'guest_id'       => $guest->id,
            'room_id'        => $room->id,
            'check_in_date'  => now()->addDays(1)->toDateString(),
            'check_out_date' => now()->addDays(3)->toDateString(),
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('bookings', [
        'guest_id' => $guest->id,
        'room_id'  => $room->id,
    ]);
    expect(Booking::latest()->first()->booking_reference)->not->toBeEmpty();
});
```

---

## Naming Conventions

| Thing | Convention |
|-------|-----------|
| Models | PascalCase singular (`Booking`, `RoomType`) |
| Tables | snake_case plural (`bookings`, `room_types`) |
| Controllers | PascalCase + `Controller` suffix (`BookingController`) |
| Form Requests | PascalCase + `Request` suffix (`CreateBookingRequest`) |
| Actions | PascalCase + `Action` suffix (`CreateBookingAction`) |
| Services | PascalCase + `Service` suffix (`AvailabilityService`) |
| Blade views | kebab-case (`booking/create.blade.php`) |
| Route names | dot-notation (`bookings.create`, `rooms.index`) |
| Policies | Model + `Policy` (`BookingPolicy`) |
