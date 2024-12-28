<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BlogController;
use App\Http\Controllers\API\FeedbackController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|----------------------------------------------------------------------
| API Routes
|----------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/blogs', [BlogController::class, 'index']);

// Route yang bisa diakses oleh semua pengguna yang terautentikasi
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/feedbacks', [FeedbackController::class, 'store']);
    Route::get('/feedbacks', [FeedbackController::class, 'index']);
    Route::get('/feedbacks/list', [FeedbackController::class, 'index2']);
});

// Route untuk Admin (hanya dapat diakses oleh pengguna dengan role admin)
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/dashboard', function () {
        return response()->json(['message' => 'Welcome Admin']);
    });

    // users management
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::patch('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

    // blogs management
    Route::get('/blogs/{id}', [BlogController::class, 'show']);
    Route::post('/blogs', [BlogController::class, 'store']);
    Route::patch('/blogs/{id}', [BlogController::class, 'update']);
    Route::delete('/blogs/{id}', [BlogController::class, 'destroy']);

    // Feedback management
    Route::delete('/feedbacks/{id}', [FeedbackController::class, 'destroy']);
});


// Route untuk User (hanya dapat diakses oleh pengguna dengan role user)
Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
    // Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user-dashboard', function () {
        return response()->json(['message' => 'Welcome User']);
    })->name('user.dashboard');

    Route::patch('/user/update-profile', [UserController::class, 'updateProfile']);
});
