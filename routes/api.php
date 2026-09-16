<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RecipeController;

Route::get("/recipes", [RecipeController::class, "index"]);
Route::post("/recipes", [RecipeController::class, "store"]);
Route::get("/recipes/{recipe}", [RecipeController::class, "show"]);
Route::put("/recipes/{recipe}", [RecipeController::class, "update"]);
Route::delete("/recipes/{recipe}", [RecipeController::class, "destroy"]);
