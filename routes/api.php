<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RecipeController;
use App\Http\Controllers\Api\AuthController;

Route::post("/register", [AuthController::class, "register"]);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware("auth:sanctum")->group(function(){
    Route::get("/recipes", [RecipeController::class, "index"]);
    Route::post("/recipes", [RecipeController::class, "store"]);
    Route::get("/recipes/{recipe}", [RecipeController::class, "show"]);
    Route::put("/recipes/{recipe}", [RecipeController::class, "update"]);
    Route::delete("/recipes/{recipe}", [RecipeController::class, "destroy"]);
});
