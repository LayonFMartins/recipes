<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Http\Resources\RecipeResource;

class RecipeController extends Controller
{
    public function index(){
        return RecipeResource::collection(Recipe::all());
    }

    public function store(StoreRecipeRequest $request){
        $recipe = Recipe::create($request->validated());

        return (new RecipeResource($recipe))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Recipe $recipe){
        return new RecipeResource($recipe);
    }

    public function update(UpdateRecipeRequest $request, Recipe $recipe){
        $recipe->update($request->validated());
        return new RecipeResource($recipe);
    }

    public function destroy(Recipe $recipe){
        $recipe->delete();
        return response()->noContent();
    }
}
