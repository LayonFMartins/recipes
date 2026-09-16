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

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/recipes');
        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_cannot_list_recipes(): void
    {
        $response = $this->getJson('/api/recipes');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_view_a_recipe(): void
    {
        $user = User::factory()->create();

        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/recipes/{$recipe->id}");

        $response->assertStatus(200);
    }

    public function test_nonexistent_recipe_returns_404(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/recipes/99999');

        $response->assertStatus(404);
    }

    public function test_authenticated_user_can_create_a_recipe(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/recipes', [
                'name' => 'Lasanha',
                'description' => 'Lasanha de carne com molho branco',
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('recipes', [
            'name' => 'Lasanha',
            'description' => 'Lasanha de carne com molho branco',
            'user_id' => $user->id,
        ]);
    }

    public function test_recipe_creation_requires_name_and_description(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/recipes', []);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'name',
            'description',
        ]);
    }

    public function test_user_cannot_update_another_users_recipe(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $recipe = Recipe::factory()->create([
            'user_id' => $owner->id,
        ]);

        $response = $this->actingAs($otherUser, 'sanctum')
            ->putJson("/api/recipes/{$recipe->id}", [
                'name' => 'Receita alterada',
                'description' => 'Tentativa de alteração',
            ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('recipes', [
            'id' => $recipe->id,
            'name' => $recipe->name,
        ]);
    }
    public function test_user_can_update_own_recipe(): void
    {
        $user = User::factory()->create();

        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/recipes/{$recipe->id}", [
                'name' => 'Lasanha atualizada',
                'description' => 'Nova descrição',
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('recipes', [
            'id' => $recipe->id,
            'name' => 'Lasanha atualizada',
            'description' => 'Nova descrição',
            'user_id' => $user->id,
        ]);
    }

    public function test_user_can_delete_own_recipe(): void
    {
        $user = User::factory()->create();

        $recipe = Recipe::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/recipes/{$recipe->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('recipes', [
            'id' => $recipe->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_recipe(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $recipe = Recipe::factory()->create([
            'user_id' => $owner->id,
        ]);

        $response = $this->actingAs($otherUser, 'sanctum')
            ->deleteJson("/api/recipes/{$recipe->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('recipes', [
            'id' => $recipe->id,
        ]);
    }
}
