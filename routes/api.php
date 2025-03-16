<?php

use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register',[UserController::class,'register']);
Route::post('login',[UserController::class,'login']);


Route::middleware('auth:sanctum')->prefix('tasks')->group(function () {
    Route::controller(TaskController::class)->group(function () {

        Route::get('/restoreTasks', 'restoreTasks');

        Route::post('/', 'create');
        Route::get('/', 'getTasks');
        Route::get('/{id}', 'getTask');
        Route::put('/{id}','update');
        Route::delete('/{id}', 'delete');
        Route::post('searchTasks','searchTasks');
    });
});


