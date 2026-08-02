# Add Module Workflow

Use this workflow whenever adding a new feature module to the HMS. Follow each step in order.

---

## Step 1 — Define Requirements

Before writing any code:
1. Identify the FR IDs this module implements.
2. Identify which actors use this module (from project-overview.md Actor table).
3. Identify database tables needed (new or existing).
4. Document any new business rules.

---

## Step 2 — Create Migration

```bash
php artisan make:migration create_{module}_table
# or
php artisan make:migration add_{column}_to_{table}_table
```

Rules:
- Include FKs with `constrained()->cascadeOnDelete()`
- Add indexes on FK columns and frequently-queried columns
- Add `softDeletes()` if the entity can be logically deleted
- Write `down()` that fully reverses `up()`

Run: `php artisan migrate`

---

## Step 3 — Create Model

```bash
php artisan make:model {ModelName} -f
```

Model must include:
- `$fillable` array (no mass-assignment vulnerabilities)
- `$casts` array (enums, dates, booleans, decimals)
- All Eloquent relationships (`belongsTo`, `hasMany`, `hasOne`)
- Query scopes for common filtering patterns
- `SoftDeletes` trait if applicable

---

## Step 4 — Create Policy / Gate

```bash
php artisan make:policy {Model}Policy --model={Model}
```

Define all authorization methods the module needs:
- `viewAny`, `view`, `create`, `update`, `delete`
- Module-specific actions (e.g. `checkIn`, `cancel`, `export`)

Map each method to the roles that are allowed (from the Actor/Use-Case table).

---

## Step 5 — Create Service Class

Create `app/Services/{Module}Service.php` for stateful business logic.

Service classes:
- Are injected via constructor into Actions or Controllers
- Handle complex calculations, queries spanning multiple models
- Are unit-testable in isolation

---

## Step 6 — Create Action Classes

Create `app/Actions/{Verb}{Model}Action.php` for each distinct write operation.

Actions:
- Accept validated data (from Form Request)
- Wrap database writes in `DB::transaction()`
- Use `lockForUpdate()` for concurrency-critical operations
- Throw domain exceptions on business rule violations
- Return the created/updated Eloquent model

---

## Step 7 — Create Controller

```bash
php artisan make:controller {Module}Controller --resource
```

Controller rules:
- Each method injects a Form Request and an Action/Service
- No business logic — delegate entirely
- Return: `view()`, `redirect()`, or `response()->json()`
- Use named routes in redirects

---

## Step 8 — Create Form Requests

```bash
php artisan make:request Create{Model}Request
php artisan make:request Update{Model}Request
```

Each Form Request:
- Defines `authorize()` using the corresponding Policy
- Defines `rules()` with all validation rules
- Uses `prepareForValidation()` for data normalization

---

## Step 9 — Register Routes

Add route group to `routes/web.php`:
```php
Route::middleware(['auth', 'role:{allowed_roles}'])->group(function () {
    Route::resource('{module}', {Module}Controller::class);
    // any extra custom routes
});
```

Run `php artisan route:list | grep {module}` to verify routes.

---

## Step 10 — Create Blade Views

Create under `resources/views/{module}/`:
- `index.blade.php` — list view with search/filter
- `create.blade.php` — create form
- `edit.blade.php` — edit form
- `show.blade.php` — detail view (if needed)

All views must:
- Extend `layouts.app`
- Use `x-status-badge`, `x-metric-card` components where appropriate
- Apply Npontu palette classes (no hardcoded hex)
- Be responsive (mobile-friendly)

---

## Step 11 — Create Factory

Update the model's factory `{Model}Factory.php`:
- All fields populated with realistic `$this->faker` data
- States for common scenarios (e.g. `->available()`, `->cancelled()`)

---

## Step 12 — Create Seeder

Create `database/seeders/{Module}Seeder.php`:
- Seeds a reasonable number of demo records
- Relies on previously seeded dependencies
- Register in `DatabaseSeeder::run()`

---

## Step 13 — Write Feature Tests

```bash
# One test file per FR ID
tests/Feature/FR{N}{M}Test.php
```

Each test file:
1. Tests the happy path (successful operation)
2. Tests at least one error case (validation failure, authorization denied, business rule violation)
3. Asserts both HTTP response and database state
4. Uses factories for test data (not `DatabaseSeeder`)

Run: `php artisan test --filter FR{N}{M}Test`

---

## Step 14 — Update PROGRESS.md

Mark each FR ID implemented in this module:
- Change `⬜ Pending` → `✅ Done` (if tests pass and route is accessible)
- Or `🔄 In Progress` if partially done

---

## Step 15 — Commit

```bash
git add -A
git commit -m "feat({module}): implement {description} ({FR-IDs})"
```
