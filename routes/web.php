<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RegisterController;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\HttpKernel\DependencyInjection\RegisterControllerArgumentLocatorsPass;

Route::view("/", "home")->name('home');

Route::middleware(["auth"])->group(function () {
    Route::view('/activities', "activities");
    Route::view('/about', "about");
    Route::view("/contact","contact");

    Route::get('/profile/{user?}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/{user?}/add-child', [ChildController::class, 'create'])->name('child.create');
    Route::post('/profile/store-child', [ChildController::class, 'store'])->name('child.store');
    Route::get('/child/{child}/edit', [ChildController::class, 'edit'])->name('child.edit');
    Route::patch('/child/{child}', [ChildController::class, 'update'])->name('child.update');
    Route::delete('/child/{child}', [ChildController::class, 'destroy'])->name('child.destroy');

});

Route::middleware(['auth',\App\Http\Middleware\Admin::class])->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::delete('/user/{user?}', [AdminController::class, 'destroy_user'])->name('user.destroy');

});

Route::get('/register',[RegisterController::class,'create'])->name('register');
Route::post('/register',[RegisterController::class,'store']);

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout',[LoginController::class,'destroy']);
















