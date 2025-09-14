<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_search_customers_by_phone()
    {
        $user = User::factory()->create(['role' => 'operator']);
        // Предполагается, что в системе есть клиенты с разными телефонами
        User::factory()->create([
            'name' => 'Иван Клиент',
            'phone' => '+79991234567',
            'role' => 'customer',
        ]);
        User::factory()->create([
            'name' => 'Петр Клиент',
            'phone' => '+79991112233',
            'role' => 'customer',
        ]);

        $response = $this->actingAs($user)->getJson('/api/customers?phone=+79991234567');
        $response->assertStatus(200)
            ->assertJsonFragment(['phone' => '+79991234567']);
    }

    public function test_search_returns_empty_for_unknown_phone()
    {
        $user = User::factory()->create(['role' => 'operator']);
        $response = $this->actingAs($user)->getJson('/api/customers?phone=+70000000000');
        $response->assertStatus(200)
            ->assertJsonMissing(['phone' => '+70000000000']);
    }

    public function test_guest_cannot_search_customers()
    {
        $response = $this->getJson('/api/customers?phone=+79991234567');
        $response->assertStatus(401);
    }
}
