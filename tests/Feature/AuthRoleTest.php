<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => 'operator',
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/orders');
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_with_invalid_credentials_fails()
    {
        User::factory()->create([
            'email' => 'test2@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test2@example.com',
            'password' => 'wrongpass',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_logout()
    {
        $user = User::factory()->create();
        $this->be($user);
        $response = $this->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_operator_can_access_orders()
    {
        $user = User::factory()->create(['role' => 'operator']);
        $response = $this->actingAs($user)->get('/orders');
        $response->assertStatus(200);
    }

    public function test_manager_can_access_orders()
    {
        $user = User::factory()->create(['role' => 'manager']);
        $response = $this->actingAs($user)->get('/orders');
        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_orders()
    {
        $response = $this->get('/orders');
        $response->assertRedirect('/login');
    }

    public function test_operator_cannot_access_manager_routes()
    {
        $user = User::factory()->create(['role' => 'operator']);
        $response = $this->actingAs($user)->get('/manager-only');
        $response->assertStatus(403);
    }

    public function test_manager_can_access_manager_routes()
    {
        $user = User::factory()->create(['role' => 'manager']);
        $response = $this->actingAs($user)->get('/manager-only');
        $response->assertStatus(200);
    }
}
