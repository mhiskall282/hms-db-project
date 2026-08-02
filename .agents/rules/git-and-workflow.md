# Git & Workflow — Always On

These rules govern commit hygiene, branching, and progress tracking for the HMS project.

---

## Commit Message Format

Use **Conventional Commits** with FR ID references:

```
<type>(<scope>): <short description> (<FR-ID(s)>)

[optional body]
[optional BREAKING CHANGE]
```

### Types

| Type | When to Use |
|------|-------------|
| `feat` | New feature or FR implementation |
| `fix` | Bug fix |
| `test` | Adding or fixing tests |
| `style` | UI/CSS changes |
| `refactor` | Code restructuring without behavior change |
| `docs` | Documentation only |
| `chore` | Build, config, dependency updates |
| `seed` | Database seeder / factory changes |

### Examples

```
feat(auth): implement login with RBAC (FR-1.1, FR-1.2)
feat(rooms): add room management CRUD (FR-2.1, FR-2.2)
feat(booking): add availability search with overlap prevention (FR-4.1, FR-2.4)
feat(checkinout): implement check-in and check-out flow (FR-5.1, FR-5.2, FR-5.3)
feat(billing): add invoice generation and payment recording (FR-6.1, FR-6.2, FR-6.3, FR-6.4)
feat(dashboard): implement manager reporting dashboard (FR-7.1, FR-7.2, FR-7.3, FR-7.4)
test(booking): add feature tests for double-booking prevention (FR-2.4, FR-4.2)
fix(billing): prevent payment exceeding invoice total (FR-6.5)
style(ui): apply Npontu branding palette across all views
seed(demo): add demo users for all 6 roles with sample bookings
```

### Rules

1. Always reference at least one FR ID when touching functional code.
2. Multiple FR IDs may be referenced if a single commit spans related requirements.
3. Never batch unrelated modules in one commit — commit per completed module.
4. Commit message subject line must be ≤ 72 characters.

---

## Branching Strategy

For a student project with 10 team members, use a simplified Git Flow:

```
main          — stable, demo-ready code only
develop       — integration branch, merged PRs land here
feature/<name> — per-module feature branches (e.g. feature/booking-module)
fix/<name>    — bug fix branches
```

**Commit directly to `main`** is acceptable for the lead engineer running the initial build sprint. After the base app is built, team members should use feature branches.

---

## PROGRESS.md — Must Be Kept Current

The `/PROGRESS.md` file at the project root is the **single source of truth** for delivery status. Update it after every module is completed.

Format:
```markdown
## FR Status

| FR ID | Description | Status | Notes |
|-------|-------------|--------|-------|
| FR-1.1 | Login with email/password | ✅ Done | Implemented via Breeze |
| FR-2.1 | Room CRUD | ⬜ Pending | |
```

Statuses: `✅ Done` | `🔄 In Progress` | `⬜ Pending` | `⚠️ Partial` | `❌ Blocked`

**Rule:** No FR ID may be marked ✅ Done without:
1. A passing feature test covering both happy path and an error case.
2. The route being accessible via the browser with demo seed data.

---

## Pre-Commit Checklist

Before every commit:
- [ ] `php artisan test` passes with no failures
- [ ] No `dd()`, `dump()`, `var_dump()`, or `echo` debug statements left in code
- [ ] No hardcoded hex colors in Blade files
- [ ] No raw passwords or PII in any log output
- [ ] PROGRESS.md updated for any newly completed FRs
- [ ] Commit message includes FR ID(s)

---

## Tagging Releases

Before the demo/submission:
```bash
git tag -a v1.0.0 -m "Final Year Project submission — all FR-1 through FR-7 implemented"
git push origin v1.0.0
```
