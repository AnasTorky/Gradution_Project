<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\VideoController;
Route::get('/test',function(){
    return "hello,world";
});
Route::post('/register', [RegisterController::class, 'store']);
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/children', [ChildController::class, 'store']);
    Route::put('/children/{child}', [ChildController::class, 'update']);
    Route::delete('/children/{child}', [ChildController::class, 'destroy']);
    Route::get('/activities', [ActivityController::class, 'index']);
    Route::get('/activities/{id}', [ActivityController::class, 'show']);
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::post('/upload-video', [VideoController::class, 'store']);
});

Route::middleware(['auth:sanctum', \App\Http\Middleware\Admin::class])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::get('/admin/users', [AdminController::class, 'users']);
    Route::delete('/admin/users/{user}', [AdminController::class, 'destroy_user']);
    Route::put('/admin/users/{id}/role', [AdminController::class, 'updateRole']);
    Route::get('/admin/activities', [AdminController::class, 'activities']);
    Route::post('/admin/activities', [ActivityController::class, 'store']);
    Route::put('/admin/activities/{id}', [ActivityController::class, 'update']);
    Route::delete('/admin/activities/{id}', [ActivityController::class, 'destroy']);
    Route::get('/admin/categories', [AdminController::class, 'categories']);
    Route::post('/admin/categories', [CategoryController::class, 'store']);
    Route::put('/admin/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/admin/categories/{id}', [CategoryController::class, 'destroy']);
});
