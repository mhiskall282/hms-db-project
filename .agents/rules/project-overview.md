# Project Overview — Always On

This rule file is loaded for every task. It contains the authoritative functional requirements,
non-functional requirements, core entities, and actor use cases for the HMS project.

---

## Functional Requirements

### Module 1 — Authentication & Access Control

| ID | Requirement |
|----|-------------|
| FR-1.1 | Login with username/email + password. |
| FR-1.2 | Enforce RBAC (Admin, Manager, Receptionist, Housekeeping, Accountant). |
| FR-1.3 | Admin can create/edit/deactivate/delete staff accounts. |
| FR-1.4 | Auto-logout after inactivity (configurable session lifetime + idle timer). |

### Module 2 — Room Management

| ID | Requirement |
|----|-------------|
| FR-2.1 | Manager/Admin can add/edit/remove rooms (number, type, rate, capacity). |
| FR-2.2 | Track room status: Available, Occupied, Reserved, Under Maintenance, Dirty/Clean. |
| FR-2.3 | Housekeeping can update room status after cleaning/maintenance. |
| FR-2.4 | Prevent double-booking for overlapping dates on the same room. |

### Module 3 — Guest Management

| ID | Requirement |
|----|-------------|
| FR-3.1 | Register guest (name, contact, ID number, nationality). |
| FR-3.2 | Search guest by name, phone, or ID. |
| FR-3.3 | Maintain guest booking history. |

### Module 4 — Booking & Reservations

| ID | Requirement |
|----|-------------|
| FR-4.1 | Search room availability by date range and room type. |
| FR-4.2 | Create booking linked to a guest profile. |
| FR-4.3 | Modify/cancel a booking under defined cancellation rules. |
| FR-4.4 | Generate a unique booking reference number per reservation. |
| FR-4.5 | Send booking confirmation (on-screen and/or email). |

### Module 5 — Check-In / Check-Out

| ID | Requirement |
|----|-------------|
| FR-5.1 | Check-in against an existing booking → room becomes Occupied. |
| FR-5.2 | Check-out → room becomes Dirty/Available for cleaning. |
| FR-5.3 | Record actual check-in/check-out timestamps. |

### Module 6 — Billing, Payments & Invoices

| ID | Requirement |
|----|-------------|
| FR-6.1 | Auto-calculate room charges from rate × length of stay. |
| FR-6.2 | Add extra charges (room service, laundry, etc.) to a bill. |
| FR-6.3 | Record payments: cash, card, or mobile money (all simulated). |
| FR-6.4 | Generate a printable/exportable invoice or receipt on full payment. |
| FR-6.5 | Track outstanding balances for partial payments. |

### Module 7 — Reporting & Dashboard

| ID | Requirement |
|----|-------------|
| FR-7.1 | Occupancy report for a date range. |
| FR-7.2 | Revenue report (bookings + additional services). |
| FR-7.3 | Dashboard: current occupancy, today's check-ins/outs, revenue. |
| FR-7.4 | Export reports as PDF or CSV. |

---

## Non-Functional Requirements

| Category | Requirement |
|----------|-------------|
| Performance | Standard actions respond within 2 seconds under normal load. |
| Usability | Intuitive for staff with minimal training; clear navigation and status cues. |
| Security | Encrypt stored passwords (bcrypt/argon2 via Laravel default); protect guest PII. |
| Reliability | No double-booking of the same room for overlapping dates — enforced with DB-level constraint or transaction + row locking, not just app logic. |
| Availability | Single-property deployment; minimal downtime during operating hours. |
| Scalability | Schema must accommodate growth in rooms/guests/bookings without redesign. |
| Maintainability | Modular architecture: Actions/Services layer, thin controllers, Form Request validation. |
| Portability | Responsive web app; works on desktop and tablet browsers. |

---

## Core Entities (Minimum Schema)

> Expand every table with proper foreign keys, indexes, soft deletes where appropriate, and `created_at`/`updated_at` timestamps.

### 1. `guests`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| name | string | |
| phone | string | |
| email | string nullable | |
| id_number | string | national ID or passport |
| nationality | string | |
| notes | text nullable | |
| created_at / updated_at | timestamps | |
| deleted_at | timestamp nullable | soft delete |

### 2. `room_types`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| name | string | Single / Double / Suite |
| base_rate | decimal(10,2) | per night |
| capacity | tinyint | max guests |
| description | text nullable | |

### 3. `rooms`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| room_number | string unique | |
| room_type_id | FK → room_types | |
| status | enum | available, occupied, reserved, maintenance, dirty |
| floor | tinyint | |

### 4. `bookings`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| booking_reference | string unique | generated UUID prefix |
| guest_id | FK → guests | |
| room_id | FK → rooms | |
| check_in_date | date | |
| check_out_date | date | |
| status | enum | pending, confirmed, checked_in, checked_out, cancelled |
| created_by | FK → users | staff who made the booking |
| notes | text nullable | |
| deleted_at | timestamp nullable | soft delete for cancellations |

### 5. `check_in_outs`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| booking_id | FK → bookings unique | one record per booking |
| actual_check_in_at | timestamp nullable | |
| actual_check_out_at | timestamp nullable | |
| checked_in_by | FK → users nullable | |
| checked_out_by | FK → users nullable | |

### 6. `invoices`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| booking_id | FK → bookings unique | |
| subtotal | decimal(10,2) | room charges + services |
| tax | decimal(10,2) | default 0.00 for student deploy |
| total | decimal(10,2) | subtotal + tax |
| status | enum | unpaid, partial, paid |
| issued_at | timestamp | |

### 7. `payments`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| invoice_id | FK → invoices | |
| amount | decimal(10,2) | must not exceed invoice.total − already paid |
| method | enum | cash, card, mobile_money |
| paid_at | timestamp | |
| recorded_by | FK → users | |
| notes | string nullable | |

### 8. `additional_services`
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| booking_id | FK → bookings | |
| invoice_id | FK → invoices nullable | linked after invoice creation |
| name | string | e.g. "Room Service", "Laundry" |
| amount | decimal(10,2) | |
| added_by | FK → users | |
| added_at | timestamp | |

### 9. `users` (Staff/User)
| Column | Type | Notes |
|--------|------|-------|
| id | bigint unsigned PK | |
| name | string | |
| email | string unique | |
| password | string | bcrypt hashed |
| is_active | boolean default true | |
| email_verified_at | timestamp nullable | |
| remember_token | string nullable | |
| created_at / updated_at | timestamps | |

> Spatie `roles` and `permissions` tables are created by the package migration.

---

## Actors & Use Cases

| Actor | Allowed Use Cases |
|-------|------------------|
| **Guest** | Search room availability; view own booking status; view own invoice |
| **Receptionist** | Register guest; create/modify/cancel booking; check-in/out; generate invoice |
| **Housekeeping** | View assigned/dirty rooms; update room status to clean/available |
| **Accountant** | Record payment; generate receipt; view outstanding balances |
| **Manager** | View occupancy/revenue reports; manage room rates and room types; manage staff accounts |
| **Administrator** | All Manager permissions + manage user accounts/roles; configure system settings |

---

## Traceability Matrix (Quick Lookup)

| FR ID | Module | Actor(s) |
|-------|--------|----------|
| FR-1.1 | Auth | All |
| FR-1.2 | Auth/RBAC | All |
| FR-1.3 | Auth/RBAC | Admin |
| FR-1.4 | Auth | All |
| FR-2.1 | Rooms | Manager, Admin |
| FR-2.2 | Rooms | All (read); Housekeeping (write status) |
| FR-2.3 | Rooms | Housekeeping |
| FR-2.4 | Booking | System (enforced) |
| FR-3.1 | Guests | Receptionist |
| FR-3.2 | Guests | Receptionist |
| FR-3.3 | Guests | Receptionist, Manager |
| FR-4.1 | Booking | Receptionist, Guest |
| FR-4.2 | Booking | Receptionist |
| FR-4.3 | Booking | Receptionist, Manager |
| FR-4.4 | Booking | System (generated) |
| FR-4.5 | Booking | System (notification) |
| FR-5.1 | Check-In/Out | Receptionist |
| FR-5.2 | Check-In/Out | Receptionist |
| FR-5.3 | Check-In/Out | System (recorded) |
| FR-6.1 | Billing | System (calculated) |
| FR-6.2 | Billing | Receptionist, Accountant |
| FR-6.3 | Billing | Accountant |
| FR-6.4 | Billing | Accountant |
| FR-6.5 | Billing | Accountant, Manager |
| FR-7.1 | Reporting | Manager, Admin |
| FR-7.2 | Reporting | Manager, Admin |
| FR-7.3 | Dashboard | Manager, Admin, Receptionist (limited) |
| FR-7.4 | Reporting | Manager, Admin |
