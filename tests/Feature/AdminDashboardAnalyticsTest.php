<?php

namespace Tests\Feature;

use App\Models\AdminDashboardAnalytics;
use App\Models\Driver;
use App\Models\Merchant;
use App\Models\Shipment;
use App\Models\User;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminDashboardAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['auth.defaults.guard' => 'api']);
        config(['sanctum.guard' => ['api']]);

        Role::firstOrCreate(['name' => 'merchant', 'guard_name' => 'api']);
        Role::firstOrCreate(['name' => 'driver', 'guard_name' => 'api']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Carbon::setTestNow(Carbon::create(2026, 5, 8, 12, 0, 0));
    }

    private function createAdminUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('admin');

        return $user;
    }

    private function loginAs(User $user, string $password = 'password'): void
    {
        app(AuthService::class)->login([
            'email' => $user->email,
            'password' => $password,
        ], $user->getRoleNames()->first());
    }

    public function test_admin_can_get_dashboard_analytics(): void
    {
        $admin = $this->createAdminUser();

        $recentDriver = Driver::factory()->create();
        $recentDriver->user->update(['last_login_at' => now()->subDays(10)]);

        $oldDriver = Driver::factory()->create();
        $oldDriver->user->update(['last_login_at' => now()->subDays(45)]);

        $recentMerchant = Merchant::factory()->create();
        $recentMerchant->user->update(['last_login_at' => now()->subWeeks(2)]);

        $oldMerchant = Merchant::factory()->create();
        $oldMerchant->user->update(['last_login_at' => now()->subDays(60)]);

        Shipment::factory()->create([
            'merchant_id' => $recentMerchant->id,
            'created_at' => Carbon::create(2026, 1, 10, 12, 0, 0),
        ]);

        Shipment::factory()->create([
            'merchant_id' => $recentMerchant->id,
            'created_at' => Carbon::create(2026, 1, 20, 12, 0, 0),
        ]);

        Shipment::factory()->create([
            'merchant_id' => $recentMerchant->id,
            'created_at' => Carbon::create(2026, 2, 5, 12, 0, 0),
        ]);

        Sanctum::actingAs($admin, ['*'], 'api');

        $response = $this->getJson('/api/admin/dashboard/analytics');

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');
        $response->assertJsonPath('data.drivers.active_drivers.count', 1);
        $response->assertJsonPath('data.drivers.inactive_drivers.count', 1);
        $response->assertJsonPath('data.merchants.active_merchants.count', 1);
        $response->assertJsonPath('data.merchants.inactive_merchants.count', 1);
        $response->assertJsonPath('data.shipments_per_month.0.month', '2026-01');
        $response->assertJsonPath('data.shipments_per_month.0.count', 2);
        $response->assertJsonPath('data.shipments_per_month.1.month', '2026-02');
        $response->assertJsonPath('data.shipments_per_month.1.count', 1);

        $this->assertNotNull(AdminDashboardAnalytics::query()->first());
    }

    public function test_login_updates_last_login_timestamp_and_hourly_refresh_uses_it(): void
    {
        $merchant = Merchant::factory()->create();
        $merchant->user->update(['last_login_at' => now()->subDays(45)]);

        $this->loginAs($merchant->user);

        $this->assertNotNull($merchant->user->fresh()->last_login_at);
        $this->assertGreaterThanOrEqual(now()->subMinute(), $merchant->user->fresh()->last_login_at);

        Artisan::call('admin:refresh-dashboard-analytics');

        $this->assertSame(1, AdminDashboardAnalytics::query()->count());

        Carbon::setTestNow(Carbon::create(2026, 6, 8, 12, 0, 0));

        $merchant->user->update(['last_login_at' => now()->subDays(10)]);
        Artisan::call('admin:refresh-dashboard-analytics');

        $snapshot = AdminDashboardAnalytics::query()->latest('analytics_month')->first();
        $this->assertNotNull($snapshot);
        $this->assertSame(1, $snapshot->active_merchants_count);
        $this->assertSame(0, $snapshot->inactive_merchants_count);

        $this->assertSame(2, AdminDashboardAnalytics::query()->count());
    }
}