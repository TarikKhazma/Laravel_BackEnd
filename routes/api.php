<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::get('tasks',[TaskController::class, 'index']);
// Route::post('tasks',[TaskController::class, 'store']);
// Route::put('tasks/{id}',[TaskController::class, 'update']);
// Route::get('tasks/{id}',[TaskController::class, 'show']);
// Route::delete('tasks/{id}',[TaskController::class, 'destroy']);


Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');


Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('profile')->group(function () {
        Route::post('', [ProfileController::class, 'store']);
        Route::get('/{id}', [ProfileController::class, 'show']);
        Route::put('/{id}', [ProfileController::class, 'update']);
    });


    Route::get('task/all', [TaskController::class, 'getAllTasks'])->middleware('CheckUser');

    Route::get('user/{id}/profile', [UserController::class, 'getProfile']);
    Route::get('user/{id}/tasks', [UserController::class, 'getUserTasks']);
    
    Route::get('task/ordered/{order?}', [TaskController::class, 'getTasksByPirority'])->whereIn('order', ['asc', 'desc']);

    Route::get('task/favorites', [TaskController::class, 'getFavoriteTasks']);
    Route::post('task/{taskId}/favorite', [TaskController::class, 'addToFavorites']);
    Route::delete('task/{taskId}/favorite', [TaskController::class, 'removeFromFavorites']);

    Route::apiResource('tasks', TaskController::class)->parameters(['tasks' => 'taskId']);
    Route::get('task/{taskId}/user', [TaskController::class, 'getTaskUser']);
    Route::post('tasks/{taskId}/categories', [TaskController::class, 'addCategoriesToTask']);
});
