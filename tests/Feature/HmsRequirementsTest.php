<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Guest;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HmsRequirementsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $receptionist;
    protected User $manager;
    protected User $housekeeper;
    protected User $accountant;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        foreach (['admin', 'manager', 'receptionist', 'housekeeping', 'accountant'] as $role) {
            Role::create(['name' => $role, 'guard_name' => 'web']);
        }

        $this->admin        = User::factory()->create(['is_active' => true])->assignRole('admin');
        $this->receptionist = User::factory()->create(['is_active' => true])->assignRole('receptionist');
        $this->manager      = User::factory()->create(['is_active' => true])->assignRole('manager');
        $this->housekeeper  = User::factory()->create(['is_active' => true])->assignRole('housekeeping');
        $this->accountant   = User::factory()->create(['is_active' => true])->assignRole('accountant');
    }

    // ==========================================
    // Auth & RBAC (FR-1.1, FR-1.2, FR-1.3, FR-1.4)
    // ==========================================

    /** @test */
    public function fr_1_1_user_can_login_with_valid_credentials()
    {
        $response = $this->post('/login', [
            'email'    => $this->admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->admin);
    }

    /** @test */
    public function fr_1_2_role_based_access_control_restricts_unauthorized_routes()
    {
        // Housekeeper trying to access User Management (admin-only)
        $response = $this->actingAs($this->housekeeper)->get('/users');
        $response->assertStatus(403);
    }

    /** @test */
    public function fr_1_3_admin_can_deactivate_user_and_deactivated_user_cannot_login()
    {
        $user = User::factory()->create(['is_active' => true])->assignRole('receptionist');

        $this->actingAs($this->admin)->patch("/users/{$user->id}/deactivate");

        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_active' => false]);

        // Logout admin session first
        auth()->logout();

        // Attempt login as deactivated user
        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // ==========================================
    // Room Management (FR-2.1, FR-2.2, FR-2.3, FR-2.4)
    // ==========================================

    /** @test */
    public function fr_2_1_manager_can_create_room_type_and_room()
    {
        $type = RoomType::create(['name' => 'Deluxe Suite', 'base_rate' => 150.00, 'capacity' => 2]);

        $response = $this->actingAs($this->manager)->post('/rooms', [
            'room_number'  => '101',
            'room_type_id' => $type->id,
            'status'       => 'available',
            'floor'        => 1,
        ]);

        $response->assertRedirect('/rooms');
        $this->assertDatabaseHas('rooms', ['room_number' => '101']);
    }

    /** @test */
    public function fr_2_3_housekeeping_can_update_room_status()
    {
        $type = RoomType::create(['name' => 'Standard', 'base_rate' => 100.00, 'capacity' => 2]);
        $room = Room::create(['room_number' => '201', 'room_type_id' => $type->id, 'status' => 'dirty', 'floor' => 2]);

        $response = $this->actingAs($this->housekeeper)->patch("/rooms/{$room->id}/status", [
            'status' => 'available',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('rooms', ['id' => $room->id, 'status' => 'available']);
    }

    // ==========================================
    // Guest Management (FR-3.1, FR-3.2, FR-3.3)
    // ==========================================

    /** @test */
    public function fr_3_1_receptionist_can_register_new_guest()
    {
        $response = $this->actingAs($this->receptionist)->post('/guests', [
            'name'        => 'Kofi Annan',
            'phone'       => '+233240000000',
            'email'       => 'kofi@example.com',
            'id_number'   => 'GHA-999888',
            'nationality' => 'Ghanaian',
        ]);

        $this->assertDatabaseHas('guests', ['id_number' => 'GHA-999888']);
    }

    // ==========================================
    // Booking & Invariants (FR-4.1, FR-4.2, FR-4.3)
    // ==========================================

    /** @test */
    public function fr_4_2_receptionist_can_create_booking()
    {
        $type  = RoomType::create(['name' => 'Single', 'base_rate' => 80.00, 'capacity' => 1]);
        $room  = Room::create(['room_number' => '301', 'room_type_id' => $type->id, 'status' => 'available', 'floor' => 3]);
        $guest = Guest::create(['name' => 'Ama Mensah', 'phone' => '+2332000000', 'id_number' => 'GHA-111', 'nationality' => 'Ghanaian']);

        $response = $this->actingAs($this->receptionist)->post('/bookings', [
            'guest_id'       => $guest->id,
            'room_id'        => $room->id,
            'check_in_date'  => today()->toDateString(),
            'check_out_date' => today()->addDays(2)->toDateString(),
        ]);

        $this->assertDatabaseHas('bookings', ['guest_id' => $guest->id, 'room_id' => $room->id, 'status' => 'confirmed']);
    }

    /** @test */
    public function fr_4_2_anti_double_booking_invariant_prevents_overlapping_reservation()
    {
        $type  = RoomType::create(['name' => 'Single', 'base_rate' => 80.00, 'capacity' => 1]);
        $room  = Room::create(['room_number' => '302', 'room_type_id' => $type->id, 'status' => 'available', 'floor' => 3]);
        $guest = Guest::create(['name' => 'Ama Mensah', 'phone' => '+2332000000', 'id_number' => 'GHA-111', 'nationality' => 'Ghanaian']);

        // First booking
        Booking::create([
            'booking_reference' => 'HMS-TEST01',
            'guest_id'          => $guest->id,
            'room_id'           => $room->id,
            'check_in_date'     => today()->toDateString(),
            'check_out_date'    => today()->addDays(3)->toDateString(),
            'status'            => 'confirmed',
            'created_by'        => $this->receptionist->id,
        ]);

        // Attempt second overlapping booking
        $response = $this->actingAs($this->receptionist)->post('/bookings', [
            'guest_id'       => $guest->id,
            'room_id'        => $room->id,
            'check_in_date'  => today()->addDay()->toDateString(),
            'check_out_date' => today()->addDays(4)->toDateString(),
        ]);

        $response->assertSessionHas('error');
    }

    // ==========================================
    // Check-In / Check-Out (FR-5.1, FR-5.2, FR-5.3)
    // ==========================================

    /** @test */
    public function fr_5_1_check_in_updates_booking_and_room_status()
    {
        $type  = RoomType::create(['name' => 'Single', 'base_rate' => 80.00, 'capacity' => 1]);
        $room  = Room::create(['room_number' => '401', 'room_type_id' => $type->id, 'status' => 'reserved', 'floor' => 4]);
        $guest = Guest::create(['name' => 'Ama Mensah', 'phone' => '+2332000000', 'id_number' => 'GHA-111', 'nationality' => 'Ghanaian']);

        $booking = Booking::create([
            'booking_reference' => 'HMS-TEST02',
            'guest_id'          => $guest->id,
            'room_id'           => $room->id,
            'check_in_date'     => today()->toDateString(),
            'check_out_date'    => today()->addDays(2)->toDateString(),
            'status'            => 'confirmed',
            'created_by'        => $this->receptionist->id,
        ]);

        $response = $this->actingAs($this->receptionist)->patch("/bookings/{$booking->id}/check-in");

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'checked_in']);
        $this->assertDatabaseHas('rooms', ['id' => $room->id, 'status' => 'occupied']);
    }

    /** @test */
    public function fr_5_2_check_out_sets_room_to_dirty_and_generates_invoice()
    {
        $type  = RoomType::create(['name' => 'Single', 'base_rate' => 80.00, 'capacity' => 1]);
        $room  = Room::create(['room_number' => '402', 'room_type_id' => $type->id, 'status' => 'occupied', 'floor' => 4]);
        $guest = Guest::create(['name' => 'Ama Mensah', 'phone' => '+2332000000', 'id_number' => 'GHA-111', 'nationality' => 'Ghanaian']);

        $booking = Booking::create([
            'booking_reference' => 'HMS-TEST03',
            'guest_id'          => $guest->id,
            'room_id'           => $room->id,
            'check_in_date'     => today()->subDays(2)->toDateString(),
            'check_out_date'    => today()->toDateString(),
            'status'            => 'checked_in',
            'created_by'        => $this->receptionist->id,
        ]);

        $response = $this->actingAs($this->receptionist)->patch("/bookings/{$booking->id}/check-out");

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'checked_out']);
        $this->assertDatabaseHas('rooms', ['id' => $room->id, 'status' => 'dirty']);
        $this->assertDatabaseHas('invoices', ['booking_id' => $booking->id]);
    }

    // ==========================================
    // Billing & Payments (FR-6.1, FR-6.3, FR-6.4)
    // ==========================================

    /** @test */
    public function fr_6_3_accountant_can_record_payment_and_update_invoice_status()
    {
        $type  = RoomType::create(['name' => 'Single', 'base_rate' => 100.00, 'capacity' => 1]);
        $room  = Room::create(['room_number' => '501', 'room_type_id' => $type->id, 'status' => 'available', 'floor' => 5]);
        $guest = Guest::create(['name' => 'Ama Mensah', 'phone' => '+2332000000', 'id_number' => 'GHA-111', 'nationality' => 'Ghanaian']);

        $booking = Booking::create([
            'booking_reference' => 'HMS-TEST04',
            'guest_id'          => $guest->id,
            'room_id'           => $room->id,
            'check_in_date'     => today()->subDays(2)->toDateString(),
            'check_out_date'    => today()->toDateString(),
            'status'            => 'checked_out',
            'created_by'        => $this->receptionist->id,
        ]);

        $invoice = Invoice::create([
            'booking_id'      => $booking->id,
            'room_charge'     => 200.00,
            'services_charge' => 0.00,
            'subtotal'        => 200.00,
            'tax'             => 0.00,
            'total'           => 200.00,
            'status'          => 'unpaid',
            'issued_at'       => now(),
        ]);

        $response = $this->actingAs($this->accountant)->post("/invoices/{$invoice->id}/payments", [
            'amount' => 200.00,
            'method' => 'cash',
        ]);

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'paid']);
        $this->assertDatabaseHas('payments', ['invoice_id' => $invoice->id, 'amount' => 200.00]);
    }

    // ==========================================
    // Reporting (FR-7.1, FR-7.2, FR-7.3)
    // ==========================================

    /** @test */
    public function fr_7_3_manager_can_access_dashboard_metrics()
    {
        $response = $this->actingAs($this->manager)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('metrics');
    }
}
