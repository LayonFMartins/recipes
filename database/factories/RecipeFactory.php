<?php

namespace Database\Factories;

use App\Models\Recipe;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Recipe>
 */
class RecipeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = Recipe::class;
    
    public function definition(): array
    {
        return [
            "name" => fake()->sentence(3),
            "description" => fake()->paragraph(),
        ];
    }
}
