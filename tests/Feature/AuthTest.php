<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::create([
            'username' => 'testuser',
            'password' => bcrypt('password'),
            'role' => 'USUAL'
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'password'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'access_token',
                    'token_type',
                    'expires_in'
                ]);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::create([
            'username' => 'testuser',
            'password' => bcrypt('password'),
            'role' => 'USUAL'
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function user_cannot_login_with_nonexistent_username()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'nonexistent',
            'password' => 'password'
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function login_requires_username_and_password()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['username', 'password']);
    }

    /** @test */
    public function user_can_logout_with_valid_token()
    {
        $user = User::create([
            'username' => 'testuser',
            'password' => bcrypt('password'),
            'role' => 'USUAL'
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'password'
        ]);

        $token = $loginResponse->json('access_token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->postJson('/api/logout');

        $response->assertStatus(200);
    }

    /** @test */
    public function user_cannot_access_protected_route_without_token()
    {
        $response = $this->getJson('/api/contacts');

        $response->assertStatus(401);
    }

    /** @test */
    public function user_can_access_protected_route_with_valid_token()
    {
        $user = User::create([
            'username' => 'testuser',
            'password' => bcrypt('password'),
            'role' => 'USUAL'
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'password'
        ]);

        $token = $loginResponse->json('access_token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/contacts');

        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_get_their_profile_with_valid_token()
    {
        $user = User::create([
            'username' => 'testuser',
            'password' => bcrypt('password'),
            'role' => 'USUAL'
        ]);

        $loginResponse = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'password'
        ]);

        $token = $loginResponse->json('access_token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->getJson('/api/me');

        $response->assertStatus(200)
                ->assertJson([
                    'id' => $user->id,
                    'username' => $user->username,
                    'role' => $user->role
                ]);
    }
} 