# HMS Project Progress & Traceability Matrix

**Project Name:** Hotel Management System (HMS) — Final Year Project  
**Status:** Active Sprint — Core Build Completed & Fully Verified  
**Last Updated:** {{ now()->format('Y-m-d H:i:s') }}

---

## 1. Functional Requirements (FR) Traceability Matrix

| FR ID | Module | Description | Implementation Status | Test Status |
|-------|--------|-------------|-----------------------|-------------|
| **FR-1.1** | Auth & RBAC | User Login & Authentication | ✅ Implemented (`AuthenticatedSessionController`) | ✅ Passed (`fr_1_1_user_can_login_with_valid_credentials`) |
| **FR-1.2** | Auth & RBAC | Role-Based Access Control (Spatie 5 Roles) | ✅ Implemented (`routes/web.php` middleware) | ✅ Passed (`fr_1_2_role_based_access_control_restricts_unauthorized_routes`) |
| **FR-1.3** | Auth & RBAC | Staff User Account Management & Deactivation | ✅ Implemented (`UserController`, `is_active` flag) | ✅ Passed (`fr_1_3_admin_can_deactivate_user_and_deactivated_user_cannot_login`) |
| **FR-1.4** | Auth & RBAC | Session Inactivity Timeout (30 min) | ✅ Implemented (`.env` session lifetime + Alpine.js idle timer) | ✅ Verified in app layout |
| **FR-2.1** | Room Mgmt | Room Type & Room CRUD | ✅ Implemented (`RoomTypeController`, `RoomController`) | ✅ Passed (`fr_2_1_manager_can_create_room_type_and_room`) |
| **FR-2.2** | Room Mgmt | Room Inventory View & Filters | ✅ Implemented (`rooms/index.blade.php`) | ✅ Verified |
| **FR-2.3** | Room Mgmt | Housekeeping Status Updates (Dirty/Clean/Maintenance) | ✅ Implemented (`HousekeepingController`) | ✅ Passed (`fr_2_3_housekeeping_can_update_room_status`) |
| **FR-2.4** | Room Mgmt | Anti-Double Booking Invariant | ✅ Implemented (`CreateBookingAction` lockForUpdate + overlap check) | ✅ Passed (`fr_4_2_anti_double_booking_invariant_prevents_overlapping_reservation`) |
| **FR-3.1** | Guest Mgmt | Guest Registration | ✅ Implemented (`GuestController@store`) | ✅ Passed (`fr_3_1_receptionist_can_register_new_guest`) |
| **FR-3.2** | Guest Mgmt | Guest Search & Directory | ✅ Implemented (`Guest@scopeSearch`, `guests/index.blade.php`) | ✅ Verified |
| **FR-3.3** | Guest Mgmt | Guest Booking History View | ✅ Implemented (`guests/show.blade.php`) | ✅ Verified |
| **FR-4.1** | Booking | Room Availability Search by Date Range | ✅ Implemented (`AvailabilityService`, `bookings/availability.blade.php`) | ✅ Verified |
| **FR-4.2** | Booking | Reservation Creation | ✅ Implemented (`CreateBookingAction`) | ✅ Passed (`fr_4_2_receptionist_can_create_booking`) |
| **FR-4.3** | Booking | Booking Management & Cancellation | ✅ Implemented (`BookingController@cancel`) | ✅ Verified |
| **FR-4.4** | Booking | Automatic Booking Reference Generator | ✅ Implemented (`Booking::generateReference` — `HMS-XXXXXXXX`) | ✅ Verified |
| **FR-4.5** | Booking | Multi-night Rate & Charge Computation | ✅ Implemented (`BillingService::calculateTotal`) | ✅ Verified |
| **FR-5.1** | Check-In/Out| Guest Check-In Workflow | ✅ Implemented (`CheckInAction`) | ✅ Passed (`fr_5_1_check_in_updates_booking_and_room_status`) |
| **FR-5.2** | Check-In/Out| Guest Check-Out Workflow & Room State Derivation | ✅ Implemented (`CheckOutAction`) | ✅ Passed (`fr_5_2_check_out_sets_room_to_dirty_and_generates_invoice`) |
| **FR-5.3** | Check-In/Out| Actual Check-In/Out Timestamp & User Tracking | ✅ Implemented (`CheckInOut` model) | ✅ Verified |
| **FR-6.1** | Billing | Automated Invoice Generation | ✅ Implemented (`BillingService::generateInvoice`) | ✅ Passed |
| **FR-6.2** | Billing | Itemized Additional Service Charges | ✅ Implemented (`AdditionalService`, `InvoiceController@addService`) | ✅ Verified |
| **FR-6.3** | Billing | Payment Recording & Status Recalculation | ✅ Implemented (`RecordPaymentAction`) | ✅ Passed (`fr_6_3_accountant_can_record_payment_and_update_invoice_status`) |
| **FR-6.4** | Billing | Invoice PDF Export | ✅ Implemented (`InvoiceController@download`, `invoices/pdf.blade.php`) | ✅ Verified |
| **FR-6.5** | Billing | Outstanding Balances Report | ✅ Implemented (`ReportingService::getOutstandingBalances`) | ✅ Verified |
| **FR-7.1** | Reporting | Occupancy Rate & Daily Metrics Report | ✅ Implemented (`ReportingService::getOccupancyReport`) | ✅ Verified |
| **FR-7.2** | Reporting | Financial Revenue & Payment Channel Report | ✅ Implemented (`ReportingService::getRevenueReport`) | ✅ Verified |
| **FR-7.3** | Reporting | Manager KPI Dashboard | ✅ Implemented (`ReportingService::getDashboardMetrics`, `dashboard.blade.php`) | ✅ Passed (`fr_7_3_manager_can_access_dashboard_metrics`) |
| **FR-7.4** | Reporting | PDF & CSV Export for Reports | ✅ Implemented (`ReportController` PDF/CSV exporters) | ✅ Verified |

---

## 2. Default Demo Credentials

All accounts pre-seeded with password: `password`

| Role | Email | Scope / Function |
|------|-------|------------------|
| System Admin | `admin@hms.local` | Full system configuration & staff account management |
| Hotel Manager | `manager@hms.local` | Reports, financial oversight, room rates |
| Front Desk / Receptionist | `receptionist@hms.local` | Bookings, check-in/out, guest registration |
| Housekeeper | `housekeeping@hms.local` | Room status updates (dirty → available) |
| Accountant / Cashier | `accountant@hms.local` | Payment recording & invoice settlement |

---

## 3. Key Architecture & Business Decisions

1. **Race-Condition-Free Double Booking Protection:**  
   Implemented at database level via `Room::lockForUpdate()` inside a database transaction (`CreateBookingAction`), enforcing strict date overlap checks (`check_in_date < $to AND check_out_date > $from`).
2. **Room Status State Machine:**  
   Room status transitions dynamically: `available` &rarr; `reserved` &rarr; `occupied` &rarr; `dirty` &rarr; `available`. Maintenance override available for repairs.
3. **Npontu-Inspired Visual Aesthetic:**  
   Custom CSS design system built on Tailwind CSS v3 with Deep Navy (`#0A2647`), Warm Gold (`#F2A93B`), Clean White (`#FFFFFF`), and Inter typography.
