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
        $recipe = $request->user()->recipes()->create($request->validated());

        return new RecipeResource($recipe);
    }

    public function show(Recipe $recipe){
        return new RecipeResource($recipe);
    }

    public function update(UpdateRecipeRequest $request, Recipe $recipe){

        $this->authorize("update", $recipe);
        $recipe->update($request->validated());
        return new RecipeResource($recipe);
    }

    public function destroy(Recipe $recipe){
        $this->authorize("delete", $recipe);
        $recipe->delete();
        return response()->noContent();
    }
}
