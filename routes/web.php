<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CheckInOutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\GuestPortalController;
use App\Http\Controllers\HousekeepingController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\GuestReviewController;
use App\Http\Controllers\RoomInspectionController;

// Public Landing Page & Online Reservations
Route::get('/', [LandingController::class, 'index'])->name('landing');
Route::post('/reserve', [LandingController::class, 'reserve'])->name('public.reserve');
Route::post('/contact', [LandingController::class, 'contact'])->name('public.contact');
Route::post('/review', [GuestReviewController::class, 'storePublic'])->name('public.review');

// Public Guest Self-Service Portal
Route::get('/portal', [GuestPortalController::class, 'lookupForm'])->name('portal.lookup');
Route::post('/portal/search', [GuestPortalController::class, 'search'])->name('portal.search');
Route::get('/portal/invoice/{booking}/download', [GuestPortalController::class, 'downloadPdf'])->name('portal.invoice.download');

// Idle logout (FR-1.4)
Route::get('/logout-idle', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login')->with('warning', 'You were logged out due to inactivity.');
})->middleware('auth')->name('logout.idle');

// ============================================================
// Authenticated Routes
// ============================================================
Route::middleware('auth')->group(function () {

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard — all authenticated staff (FR-7.3)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ----------------------------------------------------------
    // Maintenance Ticketing (Housekeeping, Admin, Manager, Receptionist)
    // ----------------------------------------------------------
    Route::middleware('role:admin|manager|housekeeping|receptionist')->group(function () {
        Route::get('maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::post('maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
        Route::patch('maintenance/{maintenanceRequest}/resolve', [MaintenanceController::class, 'resolve'])->name('maintenance.resolve');
    });

    // ----------------------------------------------------------
    // Security Audit Logs (Admin, Manager)
    // ----------------------------------------------------------
    Route::middleware('role:admin|manager')->group(function () {
        Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    });

    // ----------------------------------------------------------
    // Room Types (Manager, Admin — FR-2.1)
    // ----------------------------------------------------------
    Route::middleware('role:admin|manager')->group(function () {
        Route::resource('room-types', RoomTypeController::class);
    });

    // ----------------------------------------------------------
    // Rooms (Manager, Admin, Receptionist, Housekeeping read — FR-2.1, FR-2.2)
    // ----------------------------------------------------------
    Route::middleware('role:admin|manager|receptionist|housekeeping')->group(function () {
        Route::get('rooms', [RoomController::class, 'index'])->name('rooms.index');
        Route::get('rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    });

    Route::middleware('role:admin|manager')->group(function () {
        Route::get('rooms/create', [RoomController::class, 'create'])->name('rooms.create');
        Route::post('rooms', [RoomController::class, 'store'])->name('rooms.store');
        Route::get('rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
        Route::put('rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
        Route::delete('rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');
    });

    // ----------------------------------------------------------
    // Housekeeping & Quality Inspections (Housekeeping, Admin, Manager — FR-2.3)
    // ----------------------------------------------------------
    Route::middleware('role:admin|manager|housekeeping')->group(function () {
        Route::get('housekeeping', [HousekeepingController::class, 'index'])->name('housekeeping.index');
        Route::patch('rooms/{room}/status', [HousekeepingController::class, 'updateStatus'])->name('rooms.status');
        Route::get('rooms/{room}/inspect', [RoomInspectionController::class, 'create'])->name('rooms.inspect.create');
        Route::post('rooms/{room}/inspect', [RoomInspectionController::class, 'store'])->name('rooms.inspect.store');
    });

    // ----------------------------------------------------------
    // Guest Reviews Moderation (Manager, Admin, Receptionist)
    // ----------------------------------------------------------
    Route::middleware('role:admin|manager|receptionist')->group(function () {
        Route::get('reviews', [GuestReviewController::class, 'index'])->name('reviews.index');
        Route::patch('reviews/{review}/toggle', [GuestReviewController::class, 'togglePublish'])->name('reviews.toggle');
    });

    // ----------------------------------------------------------
    // Guests (Receptionist, Manager, Admin — FR-3.x)
    // ----------------------------------------------------------
    Route::middleware('role:admin|manager|receptionist')->group(function () {
        Route::resource('guests', GuestController::class);
    });

    // ----------------------------------------------------------
    // Bookings (Receptionist, Manager, Admin — FR-4.x)
    // ----------------------------------------------------------
    Route::middleware('role:admin|manager|receptionist')->group(function () {
        Route::get('bookings/availability', [BookingController::class, 'availability'])->name('bookings.availability');
        Route::resource('bookings', BookingController::class);
        Route::patch('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    });

    // ----------------------------------------------------------
    // Check-In / Check-Out (Receptionist, Manager, Admin — FR-5.x)
    // ----------------------------------------------------------
    Route::middleware('role:admin|manager|receptionist')->group(function () {
        Route::get('check-in-out', [CheckInOutController::class, 'index'])->name('check-in-out.index');
        Route::patch('bookings/{booking}/check-in', [CheckInOutController::class, 'checkIn'])->name('bookings.check-in');
        Route::patch('bookings/{booking}/check-out', [CheckInOutController::class, 'checkOut'])->name('bookings.check-out');
    });

    // ----------------------------------------------------------
    // Invoices & Payments (Accountant, Manager, Admin, Receptionist — FR-6.x)
    // ----------------------------------------------------------
    Route::middleware('role:admin|manager|receptionist|accountant')->group(function () {
        Route::resource('invoices', InvoiceController::class)->only(['index', 'show']);
        Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');
        Route::post('bookings/{booking}/generate-invoice', [InvoiceController::class, 'generate'])->name('invoices.generate');
        Route::post('bookings/{booking}/services', [InvoiceController::class, 'addService'])->name('bookings.add-service');
    });

    Route::middleware('role:admin|manager|accountant')->group(function () {
        Route::post('invoices/{invoice}/payments', [InvoiceController::class, 'recordPayment'])->name('invoices.payments.store');
    });

    // ----------------------------------------------------------
    // Reports (Manager, Admin, Accountant — FR-7.x)
    // ----------------------------------------------------------
    Route::middleware('role:admin|manager')->group(function () {
        Route::get('reports/occupancy', [ReportController::class, 'occupancy'])->name('reports.occupancy');
        Route::get('reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
        Route::get('reports/occupancy/pdf', [ReportController::class, 'exportOccupancyPdf'])->name('reports.occupancy.pdf');
        Route::get('reports/occupancy/csv', [ReportController::class, 'exportOccupancyCsv'])->name('reports.occupancy.csv');
        Route::get('reports/revenue/pdf', [ReportController::class, 'exportRevenuePdf'])->name('reports.revenue.pdf');
        Route::get('reports/revenue/csv', [ReportController::class, 'exportRevenueCsv'])->name('reports.revenue.csv');
    });

    Route::middleware('role:admin|manager|accountant')->group(function () {
        Route::get('reports/outstanding', [ReportController::class, 'outstanding'])->name('reports.outstanding');
    });

    // ----------------------------------------------------------
    // User Management (Admin only — FR-1.3)
    // ----------------------------------------------------------
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
        Route::patch('users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
        Route::patch('users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
    });
});

require __DIR__.'/auth.php';
