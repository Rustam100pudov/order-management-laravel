<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class OrderApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->user = User::factory()->create(['role' => 'operator']);
    }

    public function test_operator_can_create_order()
    {
        $payload = [
            'customer_name' => 'Иван Иванов',
            'customer_phone' => '+79991234567',
            'customer_email' => 'ivan@example.com',
            'customer_inn' => '1234567890',
            'company_name' => 'ООО Ромашка',
            'customer_address' => 'г. Москва, ул. Ленина, 1',
            'items' => [
                [
                    'product_name' => 'Товар 1',
                    'quantity' => 2,
                    'unit' => 'pieces',
                ],
                [
                    'product_name' => 'Товар 2',
                    'quantity' => 1,
                    'unit' => 'sets',
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->postJson('/api/orders', $payload);
        $response->assertStatus(201)
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('customer_name', 'Иван Иванов')
                    ->where('customer_phone', '+79991234567')
                    ->has('items', 2)
            );
    }

    public function test_guest_cannot_create_order()
    {
        $payload = [
            'customer_name' => 'Иван Иванов',
            'customer_phone' => '+79991234567',
            'items' => [
                [
                    'product_name' => 'Товар 1',
                    'quantity' => 2,
                    'unit' => 'pieces',
                ],
            ],
        ];
        $response = $this->postJson('/api/orders', $payload);
        $response->assertStatus(401);
    }

    public function test_validation_error_on_missing_fields()
    {
        $response = $this->actingAs($this->user)->postJson('/api/orders', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customer_name', 'customer_phone', 'items']);
    }

    public function test_operator_can_list_orders_with_filters()
    {
        Order::factory()->count(3)->create(['status' => 'new']);
        Order::factory()->count(2)->create(['status' => 'completed']);
        $response = $this->actingAs($this->user)->getJson('/api/orders?status=new');
        $response->assertStatus(200)
            ->assertJson(fn (AssertableJson $json) =>
                $json->has('data', 3)
            );
    }

    public function test_operator_can_get_statistics()
    {
        Order::factory()->count(2)->create(['status' => 'new']);
        Order::factory()->count(1)->create(['status' => 'completed']);
        $response = $this->actingAs($this->user)->getJson('/api/orders/statistics');
        $response->assertStatus(200)
            ->assertJsonStructure(['new', 'in_progress', 'completed', 'total']);
    }
}
