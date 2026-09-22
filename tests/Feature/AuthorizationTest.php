<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_access_admin_area(): void
    {
        $user = User::factory()->create();

        $this->assertFalse(
            $user->can("access-admin-area")
        );
    }

    public function test_admin_can_access_admin_area(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue(
            $admin->can("access-admin-area")
        );
    }

    public function test_regular_user_receives_403_when_accessing_admin_area(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, "sanctum")
            ->getJson("/api/admin/test");

        $response->assertForbidden();
    }

    public function test_admin_receives_200_when_accessing_admin_area(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin, "sanctum")
            ->getJson("/api/admin/test");

        $response
            ->assertOk()
            ->assertJson([
                "message" => "Área administrativa",
            ]);
    }
        
}
