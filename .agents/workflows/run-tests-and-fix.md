# Run Tests & Fix Workflow

Run this workflow whenever tests are failing or before any commit.

---

## Step 1 — Run the Full Test Suite

```bash
php artisan test --verbose
```

Note the failure count and which test files are failing.

---

## Step 2 — Categorize Failures

For each failing test, determine the root cause:

| Failure Type | Likely Root Cause | Fix Approach |
|-------------|-------------------|-------------|
| `Column not found` | Migration missing a column | Add column to migration, run `migrate:fresh` |
| `Class not found` | Missing import or file | Add `use` statement or create the file |
| `Route not defined` | Route not registered | Add to `routes/web.php` |
| `403 Forbidden` | Missing role or policy | Check `authorize()` in Form Request or Policy method |
| `422 Unprocessable` | Validation rule mismatch | Update test data to match validation rules |
| `Assertion failed` | Business logic error | Debug the Action/Service logic |
| `SQLSTATE` DB error | Schema mismatch | Check migration vs. factory data types |

---

## Step 3 — Fix Root Cause (Not Just the Assertion)

**Do not:**
- Delete the assertion to make the test pass
- Change the test to match broken behavior
- Add `->skip()` to a failing test

**Do:**
- Find the actual broken code path
- Fix the migration, model, action, or service
- Re-seed test data if schema changed: `php artisan migrate:fresh --seed --env=testing`

---

## Step 4 — Run Affected Tests

After fixing, run only the affected test(s) to verify the fix:

```bash
php artisan test --filter FR{N}{M}Test
```

Then confirm you haven't broken anything else:

```bash
php artisan test
```

---

## Step 5 — Report Summary

After all tests pass, produce a one-paragraph summary:

```
All {N} tests now pass. Fixed:
- [FR-X.X] <what was broken and how it was fixed>
- [FR-X.X] <what was broken and how it was fixed>
Remaining issues: none / <describe any known limitations>
```

---

## Useful Commands

```bash
# Run a specific test file
php artisan test tests/Feature/FR41Test.php

# Run tests matching a name pattern
php artisan test --filter "double booking"

# Show test output including dd() output
php artisan test --verbose

# Run tests in parallel (faster)
php artisan test --parallel

# Run with coverage (requires Xdebug or pcov)
php artisan test --coverage
```

---

## Common Quick Fixes

### "No query results for model"
→ Factory not seeding the expected record. Check factory `definition()` for correct FK values.

### "CSRF token mismatch" in tests
→ Add `WithoutMiddleware` trait or use `$this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)`.

### "Permission denied" in tests
→ Ensure the test user has the correct role assigned: `$user->assignRole('receptionist')`.

### Database state not matching
→ Ensure test class uses `RefreshDatabase` or `DatabaseTransactions` trait.
