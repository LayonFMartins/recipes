<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */

    public function test_user_can_register(): void
    {
        $response = $this->postJson("/api/register", [
            "name" => "Laio",
            "email" => "laio@example.com",
            "password" => "password",
            "password_confirmation" => "password",
        ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            "user",
            "token",
        ]);

        $this->assertDatabaseHas("users", [
            "email" => "laio@example.com",
        ]);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            "email" => "laio@example.com",
            "password" => "password",
        ]);

        $response = $this->postJson("/api/login", [
            "email" => "laio@example.com",
            "password" => "password",
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "user",
            "token",
        ]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            "email" => "laio@example.com",
            "password" => "password",
        ]);

        $response = $this->postJson("/api/login", [
            "email" => "laio@example.com",
            "password" => "senha-errada",
        ]);

        $response->assertStatus(401);

        $response->assertJson([
        "message" => "Credenciais Inválidas.",
    ]);
    }
}
