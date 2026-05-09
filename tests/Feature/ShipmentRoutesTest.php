<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Merchant;
use App\Models\Shipment;
use App\Models\ShipmentRoute;
use App\Models\User;
use App\Notifications\GamilOtp;
use App\Notifications\ShipmentStatusUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class ShipmentRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['auth.defaults.guard' => 'api']);
        config(['sanctum.guard' => ['api']]);

        $this->seedRoles();
        Carbon::setTestNow(Carbon::create(2026, 5, 8, 12, 0, 0));
    }

    private function seedRoles(): void
    {
        Role::firstOrCreate(['name' => 'merchant', 'guard_name' => 'api']);
        Role::firstOrCreate(['name' => 'driver', 'guard_name' => 'api']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'api']);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    private function createMerchantUser(): User
    {
        $user = User::factory()->create();

        Merchant::create([
            'user_id' => $user->id,
            'commercial_registration_number' => 'CRN-' . Str::random(8),
            'address' => 'Main St',
        ]);

        $user->assignRole('merchant');

        return $user;
    }

    private function createDriverUser(array $overrides = []): User
    {
        $user = User::factory()->create();

        Driver::create(array_merge([
            'user_id' => $user->id,
            'age' => 30,
            'gender' => 'male',
            'vehicle_type' => 'van',
            'vehicle_capacity_kg' => 1200,
            'license_plate_number' => 'ABC-' . Str::random(4),
            'driver_license_number' => 'DL-' . Str::random(6),
            'description' => 'Test driver',
        ], $overrides));

        $user->assignRole('driver');

        return $user;
    }

    private function createRouteWithCheckpoints(array $overrides = []): ShipmentRoute
    {
        $route = ShipmentRoute::create(array_merge([
            'overview_polyline' => 'encoded-polyline',
            'pick_up_location_details' => ['city' => 'Origin'],
            'delivery_location_details' => ['city' => 'Destination'],
            'pick_up_lat' => '30.1111',
            'pick_up_lng' => '31.2222',
            'delivery_lat' => '29.3333',
            'delivery_lng' => '30.4444',
            'distance' => 12.5,
            'duration_minutes' => 45,
        ], $overrides));

        $route->checkpoints()->createMany([
            [
                'type' => 'pick_up',
                'supervisor_name' => 'Pickup Manager',
                'supervisor_phone_number' => '0100000000',
                'address' => 'Pickup Address',
                'street' => 'Pickup Street',
                'building_number' => '12A',
                'notes' => 'Handle with care',
            ],
            [
                'type' => 'delivery',
                'supervisor_name' => 'Delivery Manager',
                'supervisor_phone_number' => '0111111111',
                'address' => 'Delivery Address',
                'street' => 'Delivery Street',
                'building_number' => '18B',
                'notes' => 'Leave at dock',
            ],
        ]);

        return $route;
    }

    private function createShipment(Merchant $merchant, ShipmentRoute $route, array $overrides = []): Shipment
    {
        return Shipment::create(array_merge([
            'merchant_id' => $merchant->id,
            'shipment_route_id' => $route->id,
            'goods_type' => 'Electronics',
            'who_pays' => 'sender',
            'vehicle_type' => 'van',
            'vehicle_capacity_kg' => '1000',
            'weight' => 120,
            'additional_details' => 'Fragile items',
            'is_night_shipping' => false,
            'scheduled_pickup_at' => Carbon::now()->addHours(2),
            'price' => 2500.00,
            'status' => 'created',
        ], $overrides));
    }

    public function test_merchant_can_create_shipment_with_route_and_checkpoints(): void
    {
        $user = $this->createMerchantUser();
        Sanctum::actingAs($user, ['*'], 'api');

        $payload = [
            'goods_type' => 'Furniture',
            'weight' => 50,
            'vehicle_type' => 'van',
            'vehicle_capacity_kg' => '1000',
            'who_pays' => 'sender',
            'scheduled_pickup_at' => Carbon::now()->addHour()->toDateTimeString(),
            'additional_details' => 'Fragile',
            'route' => [
                'overview_polyline' => 'encoded',
                'pick_up_lat' => '30.1',
                'pick_up_lng' => '31.2',
                'delivery_lat' => '29.3',
                'delivery_lng' => '30.4',
                'distance' => 10.5,
                'duration_minutes' => 40,
                'pick_up_checkpoint_details' => [
                    'supervisor_name' => 'Pickup Manager',
                    'supervisor_phone_number' => '0100000000',
                    'address' => 'Pickup Address',
                    'street' => 'Pickup Street',
                    'building_number' => '12A',
                    'notes' => 'Handle with care',
                ],
                'delivery_checkpoint_details' => [
                    'supervisor_name' => 'Delivery Manager',
                    'supervisor_phone_number' => '0111111111',
                    'address' => 'Delivery Address',
                    'street' => 'Delivery Street',
                    'building_number' => '18B',
                    'notes' => 'Leave at dock',
                ],
            ],
        ];

        $response = $this->postJson('/api/merchant/shipments', $payload);

        $response
            ->assertStatus(201)
            ->assertJsonPath('status', 'success');

        $shipmentId = $response->json('data.id');
        $route = ShipmentRoute::first();

        $this->assertDatabaseHas('shipments', [
            'id' => $shipmentId,
            'goods_type' => 'Furniture',
            'merchant_id' => $user->merchant->id,
        ]);

        $this->assertDatabaseHas('shipment_routes', [
            'id' => $route->id,
            'overview_polyline' => 'encoded',
            'pick_up_lat' => '30.1',
            'delivery_lat' => '29.3',
        ]);

        $this->assertDatabaseHas('checkpoints', [
            'shipment_route_id' => $route->id,
            'type' => 'pick_up',
            'supervisor_name' => 'Pickup Manager',
        ]);

        $this->assertDatabaseHas('checkpoints', [
            'shipment_route_id' => $route->id,
            'type' => 'delivery',
            'supervisor_name' => 'Delivery Manager',
        ]);
    }

    public function test_merchant_can_get_shipments_list(): void
    {
        $user = $this->createMerchantUser();
        $otherUser = $this->createMerchantUser();

        $route = $this->createRouteWithCheckpoints();
        $ownedShipment = $this->createShipment($user->merchant, $route, ['goods_type' => 'Books']);
        $otherShipment = $this->createShipment($otherUser->merchant, $route, ['goods_type' => 'Food']);

        Sanctum::actingAs($user, ['*'], 'api');

        $response = $this->getJson('/api/merchant/shipments');

        $response
            ->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $shipmentIds = collect($response->json('data'))->pluck('id')->all();

        $this->assertCount(1, $shipmentIds);
        $this->assertSame($ownedShipment->id, $shipmentIds[0]);
    }

    public function test_merchant_can_calculate_price(): void
    {
        $user = $this->createMerchantUser();
        Sanctum::actingAs($user, ['*'], 'api');

        $distance = 10;
        $weight = 2;
        $scheduled = Carbon::now()->addHour()->toDateTimeString();

        $baseFee = (float) config('shipping.pricing.base_fee');
        $perKm = (float) config('shipping.pricing.per_km');
        $perKg = (float) config('shipping.pricing.per_kg');
        $expected = round($baseFee + ($distance * $perKm) + ($weight * $perKg), 2);

        $response = $this->getJson('/api/merchant/shipments/calculate-price?distance=' . $distance . '&weight=' . $weight . '&vehicle_type=standard&scheduled_pickup_at=' . urlencode($scheduled));

        $response->assertStatus(200);

        $actual = (float) $response->json('data.estimated_price');

        $this->assertEquals($expected, $actual);
    }

    public function test_merchant_can_cancel_shipment(): void
    {
        $user = $this->createMerchantUser();
        $route = $this->createRouteWithCheckpoints();
        $shipment = $this->createShipment($user->merchant, $route, ['status' => 'created']);

        Sanctum::actingAs($user, ['*'], 'api');

        $response = $this->deleteJson('/api/merchant/shipments/' . $shipment->id);

        $response
            ->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('shipments', [
            'id' => $shipment->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_driver_can_see_available_shipments_filtered_by_vehicle(): void
    {
        $driverUser = $this->createDriverUser([
            'vehicle_type' => 'van',
            'vehicle_capacity_kg' => 1000,
        ]);
        $merchantUser = $this->createMerchantUser();

        $route = $this->createRouteWithCheckpoints();

        $matchOne = $this->createShipment($merchantUser->merchant, $route, [
            'status' => 'created',
            'vehicle_type' => 'van',
            'vehicle_capacity_kg' => '900',
        ]);
        $matchTwo = $this->createShipment($merchantUser->merchant, $route, [
            'status' => 'offered',
            'vehicle_type' => 'van',
            'vehicle_capacity_kg' => '800',
        ]);
        $this->createShipment($merchantUser->merchant, $route, [
            'status' => 'delivered',
            'vehicle_type' => 'van',
            'vehicle_capacity_kg' => '900',
        ]);
        $this->createShipment($merchantUser->merchant, $route, [
            'status' => 'created',
            'vehicle_type' => 'truck',
            'vehicle_capacity_kg' => '900',
        ]);
        $this->createShipment($merchantUser->merchant, $route, [
            'status' => 'created',
            'vehicle_type' => 'van',
            'vehicle_capacity_kg' => '1500',
        ]);

        Sanctum::actingAs($driverUser, ['*'], 'api');

        $response = $this->getJson('/api/driver/shipments/available');

        $response
            ->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(2, 'data.shipments')
            ->assertJsonFragment(['id' => $matchOne->id])
            ->assertJsonFragment(['id' => $matchTwo->id]);
    }

    public function test_driver_can_accept_shipment(): void
    {
        $driverUser = $this->createDriverUser();
        $merchantUser = $this->createMerchantUser();
        $route = $this->createRouteWithCheckpoints();

        $shipment = $this->createShipment($merchantUser->merchant, $route, [
            'status' => 'created',
            'vehicle_type' => $driverUser->driver->vehicle_type,
            'vehicle_capacity_kg' => (string) $driverUser->driver->vehicle_capacity_kg,
        ]);

        Sanctum::actingAs($driverUser, ['*'], 'api');

        $response = $this->postJson('/api/driver/shipments/accept', [
            'shipment_id' => $shipment->id,
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('shipments', [
            'id' => $shipment->id,
            'status' => 'accepted',
            'driver_id' => $driverUser->driver->id,
        ]);

        $this->assertDatabaseHas('drivers', [
            'id' => $driverUser->driver->id,
            'is_available' => 0,
        ]);
    }

    public function test_driver_can_start_trip_heading_to_pickup(): void
    {
        Notification::fake();

        $driverUser = $this->createDriverUser();
        $merchantUser = $this->createMerchantUser();
        $route = $this->createRouteWithCheckpoints();

        $shipment = $this->createShipment($merchantUser->merchant, $route, [
            'status' => 'accepted',
            'driver_id' => $driverUser->driver->id,
            'vehicle_type' => $driverUser->driver->vehicle_type,
        ]);

        Sanctum::actingAs($driverUser, ['*'], 'api');

        $response = $this->postJson('/api/driver/shipments/start', [
            'shipment_id' => $shipment->id,
            'status' => 'heading_to_pickup',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('shipments', [
            'id' => $shipment->id,
            'status' => 'heading_to_pickup',
        ]);

        Notification::assertSentTo($merchantUser, ShipmentStatusUpdated::class);
    }

    public function test_driver_can_start_trip_in_transit_sets_pickup_time(): void
    {
        Notification::fake();

        $driverUser = $this->createDriverUser();
        $merchantUser = $this->createMerchantUser();
        $route = $this->createRouteWithCheckpoints();

        $shipment = $this->createShipment($merchantUser->merchant, $route, [
            'status' => 'accepted',
            'driver_id' => $driverUser->driver->id,
            'vehicle_type' => $driverUser->driver->vehicle_type,
        ]);

        Sanctum::actingAs($driverUser, ['*'], 'api');

        $response = $this->postJson('/api/driver/shipments/start', [
            'shipment_id' => $shipment->id,
            'status' => 'in_transit',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $shipment->refresh();

        $this->assertSame('in_transit', $shipment->status);
        $this->assertNotNull($shipment->picked_up_at);

        Notification::assertSentTo($merchantUser, ShipmentStatusUpdated::class);
    }

    public function test_driver_can_request_delivery_otp_and_complete_trip(): void
    {
        Notification::fake();

        $driverUser = $this->createDriverUser();
        $merchantUser = $this->createMerchantUser();
        $route = $this->createRouteWithCheckpoints();

        $shipment = $this->createShipment($merchantUser->merchant, $route, [
            'status' => 'in_transit',
            'driver_id' => $driverUser->driver->id,
            'vehicle_type' => $driverUser->driver->vehicle_type,
        ]);

        Sanctum::actingAs($driverUser, ['*'], 'api');

        $otpResponse = $this->postJson('/api/driver/shipments/send-delivery-otp', [
            'shipment_id' => $shipment->id,
        ]);

        $otpResponse
            ->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $otp = Cache::get('shipment_otp_' . $shipment->id);

        $this->assertNotEmpty($otp);
        $this->assertSame(6, strlen($otp));

        Notification::assertSentTo($merchantUser, GamilOtp::class);

        $completeResponse = $this->postJson('/api/driver/shipments/complete', [
            'shipment_id' => $shipment->id,
            'otp' => $otp,
        ]);

        $completeResponse
            ->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $shipment->refresh();
        $driverUser->driver->refresh();

        $this->assertSame('delivered', $shipment->status);
        $this->assertNotNull($shipment->delivered_at);
        $this->assertTrue($driverUser->driver->is_available);
        $this->assertNull(Cache::get('shipment_otp_' . $shipment->id));

        Notification::assertSentTo($merchantUser, ShipmentStatusUpdated::class);
    }

    public function test_driver_can_get_shipments_log(): void
    {
        $driverUser = $this->createDriverUser();
        $otherDriverUser = $this->createDriverUser([
            'license_plate_number' => 'XYZ-' . Str::random(4),
            'driver_license_number' => 'DL-' . Str::random(6),
        ]);
        $merchantUser = $this->createMerchantUser();
        $route = $this->createRouteWithCheckpoints();

        $ownedOne = $this->createShipment($merchantUser->merchant, $route, [
            'driver_id' => $driverUser->driver->id,
            'status' => 'delivered',
        ]);
        $ownedTwo = $this->createShipment($merchantUser->merchant, $route, [
            'driver_id' => $driverUser->driver->id,
            'status' => 'cancelled',
        ]);
        $this->createShipment($merchantUser->merchant, $route, [
            'driver_id' => $otherDriverUser->driver->id,
            'status' => 'delivered',
        ]);

        Sanctum::actingAs($driverUser, ['*'], 'api');

        $response = $this->getJson('/api/driver/shipments/log');

        $response
            ->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(2, 'data.shipments')
            ->assertJsonFragment(['id' => $ownedOne->id])
            ->assertJsonFragment(['id' => $ownedTwo->id]);
    }
}
