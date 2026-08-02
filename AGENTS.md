# Hotel Management System (HMS) — Agent Rules & Context

## Project Identity

**Project Name:** Hotel Management System (HMS) — Final Year Project  
**Department:** ICT Education, Group of 10  
**Status:** Active Development — Deadline Sprint

## Product Summary

The Hotel Management System (HMS) digitises the full operational lifecycle of a single hotel property — from room-type configuration and availability management, through guest registration, reservation, and check-in/out, to billing, payment collection, and management reporting — replacing all manual/paper-based processes. The system provides role-segmented dashboards so each staff role sees only the screens and actions relevant to their function, with a responsive web interface that works equally well on desktop and tablet browsers.

## Tech Stack (Final — Not TBD)

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 11 (PHP 8.3+) |
| Frontend | Laravel Blade + Alpine.js + Tailwind CSS v3 |
| Database | MySQL via Laravel migrations + Eloquent ORM |
| Auth | Laravel Breeze (Blade stack) + Spatie laravel-permission |
| RBAC | `spatie/laravel-permission` — roles map 1:1 to actor list below |
| PDF/Export | `barryvdh/laravel-dompdf` (invoices/reports) + `maatwebsite/excel` (CSV) |
| Testing | Pest (feature tests, one per FR ID minimum) |
| Queues/Notifications | Database driver (adequate for student deployment) |

## Roles (Spatie Permission Roles)

| Role slug | Actor | Primary Scope |
|-----------|-------|---------------|
| `admin` | System Administrator | User/role management, system config |
| `manager` | Hotel Manager | Reports, room rates, staff oversight |
| `receptionist` | Receptionist / Front Desk | Bookings, check-in/out, guest registration |
| `housekeeping` | Housekeeping Staff | Room status updates |
| `accountant` | Accountant / Cashier | Payments, invoices, outstanding balances |
| `guest` | Hotel Guest | Availability search, booking status, invoice view (future portal) |

## FR-ID ↔ Module Traceability

Every PR, commit, or code file that implements or touches a requirement **must** reference its FR ID in the commit message, e.g.:  
`feat(booking): add availability search (FR-4.1)`

| Module | FR IDs |
|--------|--------|
| Auth & RBAC | FR-1.1, FR-1.2, FR-1.3, FR-1.4 |
| Room Management | FR-2.1, FR-2.2, FR-2.3, FR-2.4 |
| Guest Management | FR-3.1, FR-3.2, FR-3.3 |
| Booking | FR-4.1, FR-4.2, FR-4.3, FR-4.4, FR-4.5 |
| Check-In / Check-Out | FR-5.1, FR-5.2, FR-5.3 |
| Billing & Payments | FR-6.1, FR-6.2, FR-6.3, FR-6.4, FR-6.5 |
| Reporting & Dashboard | FR-7.1, FR-7.2, FR-7.3, FR-7.4 |

## Critical Reading Rule

> **Read `/.agents/skills/hms-domain/SKILL.md` before touching any booking, room, or billing logic.**

This rule exists because the domain has non-obvious state-machine rules (booking lifecycle, room status derivation, anti-double-booking invariants) that must be respected in every edit. Violating them creates data integrity bugs that are hard to debug and embarrassing in a graded demo.

## File Index

```
/.agents/rules/project-overview.md     — Full FR table, NFRs, entities, actors (Always On)
/.agents/rules/laravel-conventions.md  — Code standards, patterns, testing rules (Always On)
/.agents/rules/rbac-and-security.md    — Role middleware, session, security rules (Always On)
/.agents/rules/ui-branding.md          — Npontu palette, Tailwind config, layout (Always On)
/.agents/rules/git-and-workflow.md     — Commit format, PROGRESS.md maintenance (Always On)
/.agents/workflows/build-full-app.md   — End-to-end build workflow
/.agents/workflows/add-module.md       — Reusable module scaffold workflow
/.agents/workflows/run-tests-and-fix.md — Test-fix loop workflow
/.agents/workflows/deploy-local.md     — Local deployment steps
/.agents/skills/hms-domain/SKILL.md   — Booking/room/billing domain model
/.agents/skills/billing-invoicing/SKILL.md — Charge/invoice/payment rules
/.agents/skills/reporting-dashboard/SKILL.md — Dashboard metrics and export
/.agents/skills/rbac-auth/SKILL.md    — Role/permission/session rules
```
