<?php

use App\Http\Controllers\ApplicationFormController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AddTaskController;
use App\Models\ApplicationForm;
use Illuminate\Support\Facades\Route;

// API routes with prefix
Route::prefix('api')->group(function () {
    // LOGIN ROUTES
    Route::post('/signup', [UserController::class, 'store']);
    Route::post('/developer/signup', [UserController::class, 'storeDev']);
    Route::post('/login', [UserController::class, 'login']);
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/reset-password', [UserController::class, 'resetPassword']);

    // CREATES ALL CRUD ROUTES
    Route::apiResource('registration', ApplicationFormController::class);

    Route::get('/myApplication/{userId}', [ApplicationFormController::class, 'myApplications']);
    Route::get('/application-stats', [ApplicationFormController::class, 'stats']);



    // USER MANAGEMENT (Admin/Verifier only)
    Route::get('/users', [UserController::class, 'index']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::patch('/users/{id}/status', [UserController::class, 'updateStatus']);
    Route::get('/users/count', [UserController::class, 'userCount']);

    // ADD TASK ROUTE
    Route::apiResource('add_task', AddTaskController::class);
    Route::get('/add_task/view/{task_id}', [AddTaskController::class, 'showByTaskId']);
    Route::get('/tasks/all', [AddTaskController::class, 'allTasks']);
    Route::get('/tasks/chart', [AddTaskController::class, 'allTasksForChart']);


    // DEVELOPERS ROUTE
     Route::get('/developers', [AddTaskController::class, 'getDevelopers']);

     // CLIENTS ROUTE
     Route::get('/user_client', [AddTaskController::class, 'getClients']);

    // CLIENTS WITH REGISTRATION
    Route::get('/clients-with-registration', [AddTaskController::class, 'getClientsWithRegistration']);
    Route::get('/clients-with-registration', [UserController::class, 'getClientsWithRegistration']);

    // PROFILE
    Route::put('/users/{id}', [UserController::class, 'updateProfile']);

    // CURRENT USER
    Route::get('/current-user', [UserController::class, 'getCurrentUser']);
});


// Catch-all route for Vue Router
Route::get('/{any}', fn() => view('app'))->where('any', '.*');