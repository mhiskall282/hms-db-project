---
name: hms-domain
description: "Use this skill whenever working on booking, room availability, check-in/out, or the core HMS data model."
---

# HMS Domain Skill

## Purpose

This skill captures all domain-specific knowledge about the Hotel Management System's core data model, booking lifecycle, and room status invariants. Read this before making any change to booking, room, availability, or check-in/out logic.

---

## Core Entity Summary

| Entity | Table | Key Fields | Soft Delete |
|--------|-------|-----------|-------------|
| Guest | `guests` | name, phone, email, id_number, nationality | ✅ |
| RoomType | `room_types` | name, base_rate, capacity | ❌ |
| Room | `rooms` | room_number, room_type_id, status, floor | ❌ |
| Booking | `bookings` | booking_reference, guest_id, room_id, check_in_date, check_out_date, status | ✅ |
| CheckInOut | `check_in_outs` | booking_id, actual_check_in_at, actual_check_out_at | ❌ |
| Invoice | `invoices` | booking_id, subtotal, tax, total, status | ❌ |
| Payment | `payments` | invoice_id, amount, method, paid_at | ❌ |
| AdditionalService | `additional_services` | booking_id, invoice_id, name, amount | ❌ |
| User (Staff) | `users` | name, email, password, is_active | ✅ |

---

## Booking Lifecycle State Machine

```
                   ┌─────────────┐
                   │   pending   │  ← Initial state on creation
                   └──────┬──────┘
                          │ (Receptionist confirms)
                          ▼
                   ┌─────────────┐
                   │  confirmed  │
                   └──────┬──────┘
                          │ (Guest checks in — FR-5.1)
                          ▼
                   ┌─────────────┐
                   │ checked_in  │
                   └──────┬──────┘
                          │ (Guest checks out — FR-5.2)
                          ▼
                   ┌──────────────┐
                   │ checked_out  │  (= completed)
                   └──────────────┘

At any point before check-in:
     pending ─────► cancelled
     confirmed ───► cancelled
```

### State Transition Rules

1. `pending → confirmed`: Receptionist confirms the booking.
2. `confirmed → checked_in`: Actual check-in recorded; room status → `occupied` (FR-5.1).
3. `checked_in → checked_out`: Actual check-out recorded; room status → `dirty` (FR-5.2).
4. `pending|confirmed → cancelled`: Can happen up to 24 hours before check-in (free cancellation default; flag as business decision in PROGRESS.md).
5. **No state can go backwards.** A checked-out booking cannot return to checked-in.

---

## Room Status State Machine

```
                   ┌───────────┐
                   │ available │ ← Default after cleaning confirmed
                   └─────┬─────┘
          booking created │
                          ▼
                   ┌───────────┐
                   │ reserved  │ ← Booking status = confirmed (FR-2.2)
                   └─────┬─────┘
              check-in   │
                          ▼
                   ┌───────────┐
                   │ occupied  │ ← Booking status = checked_in (FR-5.1)
                   └─────┬─────┘
             check-out   │
                          ▼
                   ┌───────────┐
                   │   dirty   │ ← Booking status = checked_out (FR-5.2)
                   └─────┬─────┘
         housekeeping     │
         marks clean      ▼
                   ┌───────────┐
                   │ available │ ← (FR-2.3)
                   └───────────┘

At any time by Manager/Admin:
     any status ──► maintenance
     maintenance ─► available (after repair)
```

### Room Status Consistency Invariant

> **A room's `status` column must ALWAYS be consistent with the state of its most recent active Booking and CheckInOut record.**

This means:
- When a booking is created (confirmed), update room status to `reserved`.
- When check-in happens, update room status to `occupied` **in the same transaction**.
- When check-out happens, update room status to `dirty` **in the same transaction**.
- When housekeeping marks a room clean, update room status to `available`.
- When a booking is cancelled, revert room status to `available` (if no other active bookings for that room).

**Never** update room status in isolation without checking the booking state.

---

## Anti-Double-Booking Rule (FR-2.4)

Two bookings overlap if and only if:
```
booking_A.check_in_date  < booking_B.check_out_date
AND
booking_A.check_out_date > booking_B.check_in_date
```

The check-out date is exclusive (a guest checking out on May 10 frees the room for a guest checking in on May 10).

Enforcement:
1. **DB transaction + `lockForUpdate()`** on the room row before inserting.
2. Query for overlapping non-cancelled bookings on the same room.
3. Throw `RoomNotAvailableException` if overlap found.
4. The DB transaction ensures no race condition between concurrent requests.

---

## Booking Reference Format

Generate in `CreateBookingAction`:
```php
$reference = 'HMS-' . strtoupper(Str::random(8));
// Verify uniqueness (loop until unique, extremely unlikely to need more than 1 attempt)
while (Booking::where('booking_reference', $reference)->exists()) {
    $reference = 'HMS-' . strtoupper(Str::random(8));
}
```

---

## Key Eloquent Relationships

```php
// Room
public function roomType(): BelongsTo  { return $this->belongsTo(RoomType::class); }
public function bookings(): HasMany    { return $this->hasMany(Booking::class); }
public function activeBooking(): HasOne { return $this->hasOne(Booking::class)->whereNotIn('status', ['cancelled', 'checked_out'])->latest(); }

// Booking
public function guest(): BelongsTo    { return $this->belongsTo(Guest::class); }
public function room(): BelongsTo     { return $this->belongsTo(Room::class); }
public function checkInOut(): HasOne  { return $this->hasOne(CheckInOut::class); }
public function invoice(): HasOne     { return $this->hasOne(Invoice::class); }
public function additionalServices(): HasMany { return $this->hasMany(AdditionalService::class); }

// Guest
public function bookings(): HasMany   { return $this->hasMany(Booking::class); }

// Invoice
public function payments(): HasMany   { return $this->hasMany(Payment::class); }
public function additionalServices(): HasMany { return $this->hasMany(AdditionalService::class); }
```

---

## Availability Query

The canonical availability query (used in `AvailabilityService`):

```php
public function getAvailableRooms(Carbon $from, Carbon $to, ?int $roomTypeId = null): Collection
{
    return Room::query()
        ->with('roomType')
        ->where('status', '!=', 'maintenance')
        ->when($roomTypeId, fn($q) => $q->where('room_type_id', $roomTypeId))
        ->whereDoesntHave('bookings', function ($q) use ($from, $to) {
            $q->whereNotIn('status', ['cancelled'])
              ->where('check_in_date', '<', $to->toDateString())
              ->where('check_out_date', '>', $from->toDateString());
        })
        ->get();
}
```
