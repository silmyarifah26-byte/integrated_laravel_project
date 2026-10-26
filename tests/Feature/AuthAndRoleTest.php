<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAndRoleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        User::create([
            'name' => 'Admin Test',
            'email' => 'admin@bonekaku.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Staff Test',
            'email' => 'staff@bonekaku.test',
            'password' => Hash::make('password123'),
            'role' => 'staff',
        ]);

        User::create([
            'name' => 'Customer Test',
            'email' => 'customer@bonekaku.test',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        // Verify Customer portal content
        $response->assertSee("Masuk Pelanggan Diza's Plushie", false);
        $response->assertSee('Daftar Akun Customer Sekarang');
        $response->assertDontSee('Akun Demo Siap Pakai');
        $response->assertDontSee('admin@bonekaku.test');
        $response->assertDontSee('staff@bonekaku.test');
        $response->assertDontSee('customer@bonekaku.test');
        $response->assertDontSee('portal-tab-btn');
    }

    public function test_staff_and_admin_login_screens_can_be_rendered(): void
    {
        // 1. Staff dedicated portal
        $staffResponse = $this->get('/staff/login');
        $staffResponse->assertStatus(200);
        $staffResponse->assertSee('Portal Staff Operasional');
        $staffResponse->assertDontSee('Portal Staff Kasir & Gudang');
        $staffResponse->assertDontSee('Daftar Akun Customer Sekarang');

        // 2. Admin dedicated portal
        $adminResponse = $this->get('/admin/login');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Portal Administrator & Owner', false);
        $adminResponse->assertDontSee('Portal Pemilik & Administrator');
        $adminResponse->assertDontSee('Daftar Akun Customer Sekarang');

        // 3. Backward compatible query param redirects to separate routes
        $legacyStaff = $this->get('/login?portal=staff');
        $legacyStaff->assertRedirect(route('login.staff'));

        $legacyAdmin = $this->get('/login?portal=admin');
        $legacyAdmin->assertRedirect(route('login.admin'));
    }

    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_dashboard_and_admin_routes(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);

        $adminRouteResponse = $this->actingAs($admin)->get('/users');
        $adminRouteResponse->assertStatus(200);
    }

    public function test_staff_cannot_access_admin_only_route(): void
    {
        $staff = User::where('role', 'staff')->first();
        $this->assertNotNull($staff);

        $response = $this->actingAs($staff)->get('/users');
        $response->assertStatus(403);
    }

    public function test_customer_cannot_access_admin_or_staff_routes(): void
    {
        $customer = User::where('role', 'customer')->first();
        $this->assertNotNull($customer);

        // Cannot access admin route
        $response = $this->actingAs($customer)->get('/users');
        $response->assertStatus(403);

        // Cannot access manage route
        $responseManage = $this->actingAs($customer)->get('/purchases');
        $responseManage->assertStatus(403);
    }

    public function test_customer_can_access_dashboard(): void
    {
        $customer = User::where('role', 'customer')->first();
        $response = $this->actingAs($customer)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Riwayat Pesanan Saya');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@bonekaku.test',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_invalid_password(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'admin@bonekaku.test',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_with_mismatched_portal_role_is_rejected(): void
    {
        // Customer attempting to login via Admin Portal
        $response = $this->from('/admin/login')->post('/login', [
            'name' => 'Customer Test',
            'email' => 'customer@bonekaku.test',
            'password' => 'password123',
            'portal_role' => 'admin',
        ]);

        $response->assertRedirect('/admin/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();

        // Staff attempting to login via Admin Portal
        $staffResponse = $this->from('/admin/login')->post('/login', [
            'email' => 'staff@bonekaku.test',
            'password' => 'password123',
            'portal_role' => 'admin',
        ]);

        $staffResponse->assertRedirect('/admin/login');
        $staffResponse->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_with_matching_portal_role_succeeds(): void
    {
        // Customer logging in through Customer Portal
        $customerResponse = $this->post('/login', [
            'name' => 'Customer Test',
            'email' => 'customer@bonekaku.test',
            'password' => 'password123',
            'portal_role' => 'customer',
        ]);
        $customerResponse->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        auth()->logout();

        // Staff logging in through Staff Portal
        $staffResponse = $this->post('/login', [
            'email' => 'staff@bonekaku.test',
            'password' => 'password123',
            'portal_role' => 'staff',
        ]);
        $staffResponse->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        auth()->logout();

        // Admin logging in through Admin Portal
        $adminResponse = $this->post('/login', [
            'email' => 'admin@bonekaku.test',
            'password' => 'password123',
            'portal_role' => 'admin',
        ]);
        $adminResponse->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_customer_login_requires_name(): void
    {
        $response = $this->post('/login', [
            'portal_role' => 'customer',
            'email' => 'customer@bonekaku.test',
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertGuest();
    }

    public function test_customer_login_with_new_name_updates_account_and_isolates_orders(): void
    {
        $customer = User::where('role', 'customer')->first();

        // Previous sale under old customer name
        \App\Models\Sale::create([
            'user_id' => $customer->id,
            'customer_id' => $customer->id,
            'customer_name' => 'Customer Test',
            'sale_number' => 'INV-20261026-0001',
            'total' => 100000,
            'status' => 'completed',
            'sold_at' => now(),
        ]);

        // Customer logs in with a new name "Zahra" using the same email
        $response = $this->post('/login', [
            'portal_role' => 'customer',
            'name' => 'Zahra',
            'email' => $customer->email,
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertEquals('Zahra', auth()->user()->fresh()->name);

        // When viewing sales index, Zahra cannot see Nadia's / old customer's sale
        $salesResponse = $this->get('/sales');
        $salesResponse->assertStatus(200);
        $salesResponse->assertSee('Riwayat Pesanan Saya');
        $salesResponse->assertDontSee('INV-20261026-0001');

        // When Zahra makes an order, it appears in her history
        \App\Models\Sale::create([
            'user_id' => $customer->id,
            'customer_id' => $customer->id,
            'customer_name' => 'Zahra',
            'sale_number' => 'INV-20261026-0002',
            'total' => 150000,
            'status' => 'completed',
            'sold_at' => now(),
        ]);

        $salesResponse2 = $this->get('/sales');
        $salesResponse2->assertSee('INV-20261026-0002');
    }

    public function test_customer_can_login_without_password(): void
    {
        // Existing customer logs in passwordless
        $response = $this->post('/login', [
            'portal_role' => 'customer',
            'name' => 'Nadia Putri',
            'email' => 'customer@bonekaku.test',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertEquals('customer@bonekaku.test', auth()->user()->email);
        $this->assertTrue(auth()->user()->isCustomer());

        auth()->logout();

        // New customer registers and logs in automatically without password
        $newEmail = 'zahra.new@plushie.test';
        $newCustomerResponse = $this->post('/login', [
            'portal_role' => 'customer',
            'name' => 'Zahra Pradiza',
            'email' => $newEmail,
        ]);

        $newCustomerResponse->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => $newEmail,
            'name' => 'Zahra Pradiza',
            'role' => 'customer',
        ]);
    }

    public function test_staff_and_admin_portals_still_require_password(): void
    {
        // Staff portal without password must fail
        $staffResponse = $this->post('/login', [
            'portal_role' => 'staff',
            'email' => 'staff@bonekaku.test',
            'password' => '',
        ]);
        $staffResponse->assertSessionHasErrors('password');
        $this->assertGuest();

        // Admin portal without password must fail
        $adminResponse = $this->post('/login', [
            'portal_role' => 'admin',
            'email' => 'admin@bonekaku.test',
            'password' => '',
        ]);
        $adminResponse->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function test_csrf_token_mismatch_redirects_back_with_friendly_message(): void
    {
        \Illuminate\Support\Facades\Route::post('/test-token-mismatch', function () {
            throw new \Illuminate\Session\TokenMismatchException('CSRF token mismatch.');
        });

        $response = $this->from('/login')->post('/test-token-mismatch', [
            'portal_role' => 'customer',
            'name' => 'Nadia Customer',
            'email' => 'customer@bonekaku.test',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHas('error', 'Sesi halaman telah diperbarui, silakan klik tombol Masuk sekali lagi.');
    }
}
