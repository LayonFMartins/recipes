<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Recipe;

class RecipeTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_authenticated_user_can_list_recipes(): void
    {
        $user = User::factory()->create();

        Recipe::factory()->count(11)->create([
            "user_id" => $user->id,
        ]);

        $response = $this->actingAs($user, "sanctum")->getJson("/api/recipes");
        $response->assertStatus(200);

        $response->assertJsonStructure([
            "data" => [
                "*" => [
                    "id",
                    "name",
                    "description",
                ],
            ],
            "links",
            "meta",
        ]);

        $response->assertJsonCount(10, "data");

        $response->assertJsonPath("meta.current_page", 1);
        $response->assertJsonPath("meta.per_page", 10);
        $response->assertJsonPath("meta.total", 11);
        $response->assertJsonPath("meta.last_page", 2);
    }

    public function test_user_can_navigate_recipe_pages(): void
    {
        $user = User::factory()->create();

        Recipe::factory()->count(11)->create([
            "user_id" => $user->id,
        ]);

        $response = $this->actingAs($user, "sanctum")
            ->getJson("/api/recipes?page=2");

        $response->assertStatus(200);

        $response->assertJsonCount(1, "data");

        $response->assertJsonPath("meta.current_page", 2);
        $response->assertJsonPath("meta.last_page", 2);
    }

    public function test_unauthenticated_user_cannot_list_recipes(): void
    {
        $response = $this->getJson("/api/recipes");

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_view_a_recipe(): void
    {
        $user = User::factory()->create();

        $recipe = Recipe::factory()->create([
            "user_id" => $user->id,
        ]);

        $response = $this->actingAs($user, "sanctum")
            ->getJson("/api/recipes/{$recipe->id}");

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "data" => [
                "id",
                "name",
                "description",
            ],
        ]);

        $response->assertJsonPath("data.id", $recipe->id);
        $response->assertJsonPath("data.name", $recipe->name);
        $response->assertJsonPath("data.description", $recipe->description);
    }

    public function test_user_can_search_recipes_by_name(): void
    {
        $user = User::factory()->create();

        Recipe::factory()->create([
            "user_id" => $user->id,
            "name" => "Lasanha de carne",
        ]);

        Recipe::factory()->create([
            "user_id" => $user->id,
            "name" => "Bolo de chocolate",
        ]);

        Recipe::factory()->create([
            "user_id" => $user->id,
            "name" => "Lasanha vegetariana",
        ]);

        $response = $this->actingAs($user, "sanctum")
            ->getJson("/api/recipes?search=lasanha");

        $response->assertStatus(200);

        $response->assertJsonCount(2, "data");

        $response->assertJsonFragment([
            "name" => "Lasanha de carne",
        ]);

        $response->assertJsonFragment([
            "name" => "Lasanha vegetariana",
        ]);

        $response->assertJsonMissing([
            "name" => "Bolo de chocolate",
        ]);
    }

    public function test_nonexistent_recipe_returns_404(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, "sanctum")
            ->getJson("/api/recipes/99999");

        $response->assertStatus(404);
    }

    public function test_authenticated_user_can_create_a_recipe(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, "sanctum")
            ->postJson("/api/recipes", [
                "name" => "Lasanha",
                "description" => "Lasanha de carne com molho branco",
            ]);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            "data" => [
                "id",
                "name",
                "description",
            ],
        ]);

        $response->assertJsonPath("data.name", "Lasanha");
        $response->assertJsonPath(
            "data.description",
            "Lasanha de carne com molho branco"
        );

        $this->assertDatabaseHas("recipes", [
            "name" => "Lasanha",
            "description" => "Lasanha de carne com molho branco",
            "user_id" => $user->id,
        ]);
    }

    public function test_recipe_creation_requires_name_and_description(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, "sanctum")
            ->postJson("/api/recipes", []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            "name",
            "description",
        ]);
    }

    public function test_user_cannot_update_another_users_recipe(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $recipe = Recipe::factory()->create([
            "user_id" => $owner->id,
        ]);

        $response = $this->actingAs($otherUser, "sanctum")
            ->putJson("/api/recipes/{$recipe->id}", [
                "name" => "Receita alterada",
                "description" => "Tentativa de alteração",
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas("recipes", [
            "id" => $recipe->id,
            "name" => $recipe->name,
        ]);
    }
    public function test_user_can_update_own_recipe(): void
    {
        $user = User::factory()->create();

        $recipe = Recipe::factory()->create([
            "user_id" => $user->id,
        ]);

        $response = $this->actingAs($user, "sanctum")
            ->putJson("/api/recipes/{$recipe->id}", [
                "name" => "Lasanha atualizada",
                "description" => "Nova descrição",
            ]);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "data" => [
                "id",
                "name",
                "description",
            ],
        ]);

        $response->assertJsonPath("data.id", $recipe->id);
        $response->assertJsonPath("data.name", "Lasanha atualizada");
        $response->assertJsonPath("data.description", "Nova descrição");

        $this->assertDatabaseHas("recipes", [
            "id" => $recipe->id,
            "name" => "Lasanha atualizada",
            "description" => "Nova descrição",
            "user_id" => $user->id,
        ]);
    }

    public function test_user_can_delete_own_recipe(): void
    {
        $user = User::factory()->create();

        $recipe = Recipe::factory()->create([
            "user_id" => $user->id,
        ]);

        $response = $this->actingAs($user, "sanctum")
            ->deleteJson("/api/recipes/{$recipe->id}");

        $response->assertStatus(204);
        $response->assertNoContent();

        $this->assertDatabaseMissing("recipes", [
            "id" => $recipe->id,
        ]);
    }
    public function test_user_cannot_delete_another_users_recipe(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $recipe = Recipe::factory()->create([
            "user_id" => $owner->id,
        ]);

        $response = $this->actingAs($otherUser, "sanctum")
            ->deleteJson("/api/recipes/{$recipe->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas("recipes", [
            "id" => $recipe->id,
        ]);
    }

    public function test_admin_can_update_another_users_recipe(): void
    {
        $admin = User::factory()->admin()->create();

        $owner = User::factory()->create();

        $recipe = Recipe::factory()->create([
            "user_id" => $owner->id,
        ]);

        $response = $this->actingAs($admin, "sanctum")
            ->putJson("/api/recipes/{$recipe->id}", [
                "name" => "Receita atualizada pelo admin",
                "description" => "Descrição atualizada pelo admin",
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas("recipes", [
            "id" => $recipe->id,
            "name" => "Receita atualizada pelo admin",
            "description" => "Descrição atualizada pelo admin",
            "user_id" => $owner->id,
        ]);
    }

    public function test_admin_can_delete_another_users_recipe(): void
    {
        $admin = User::factory()->admin()->create();

        $owner = User::factory()->create();

        $recipe = Recipe::factory()->create([
            "user_id" => $owner->id,
        ]);

        $response = $this->actingAs($admin, "sanctum")
            ->deleteJson("/api/recipes/{$recipe->id}");

        $response->assertStatus(204);
        $response->assertNoContent();

        $this->assertDatabaseMissing("recipes", [
            "id" => $recipe->id,
        ]);
    }

    public function test_user_can_check_if_they_can_update_their_own_recipe(): void
    {
        $user = User::factory()->create();

        $recipe = Recipe::factory()->create([
            "user_id" => $user->id,
        ]);

        $this->assertTrue(
            $user->can("update", $recipe)
        );
    }
}
