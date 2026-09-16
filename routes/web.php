<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
//use App\Http\Requests\StoreRecipeRequest;

Route::get('/', [HomeController::class, 'index']);

// Route::post('/recipes', function (StoreRecipeRequest $request) {
//     return $request->validated();
// });