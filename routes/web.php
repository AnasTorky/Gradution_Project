<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChildController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\VideoController;
use App\Models\Activity;
use App\Models\Category;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\HttpKernel\DependencyInjection\RegisterControllerArgumentLocatorsPass;


Route::get('/register',[RegisterController::class,'create'])->name('register');
Route::post('/register',[RegisterController::class,'store'])->name('register.store');

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout',[LoginController::class,'destroy']);

Route::view("/", "home")->name('home');

Route::middleware(["auth"])->group(function () {
    Route::view('/about', "about");
    Route::view("/contact","contact");

    Route::get('/profile/{user?}', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/{user?}/add-child', [ChildController::class, 'create'])->name('child.create');
    Route::post('/profile/store-child', [ChildController::class, 'store'])->name('child.store');
    Route::get('/child/{child}/edit', [ChildController::class, 'edit'])->name('child.edit');
    Route::patch('/child/{child}', [ChildController::class, 'update'])->name('child.update');
    Route::delete('/child/{child}', [ChildController::class, 'destroy'])->name('child.destroy');


    Route::get('/upload-video', [VideoController::class, 'create'])->name('video.upload');
    Route::post('/upload-video', [VideoController::class, 'store'])->name('video.store');

    //show all activities
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
    //show one
    Route::get('/activities/{id}', function ($id) {
        $activity = Activity::findOrFail($id);
        $categories = Category::all();
        return view('activities.show', compact('activity','categories'));
    })->name('activities.show');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    //show one
    Route::get('/categories/{category}', function (Category $category) {
        $activities = $category->activities()->paginate(10); // Changed from get() to paginate()
        return view('categories.show', compact('category', 'activities'));
    })->name('categories.show');
});

Route::middleware(['auth',\App\Http\Middleware\Admin::class])->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::delete('/user/{user?}', [AdminController::class, 'destroy_user'])->name('user.destroy');
// admin activities
    Route::get('/admin/activities', [AdminController::class, 'activities'])->name('admin.activities');
    Route::get('/activities/add', [ActivityController::class, 'create'])->name('activities.create');
    Route::post('/activities/store', [ActivityController::class, 'store'])->name('activities.store');
    Route::get('/activities/{id}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
    Route::put('/activities/{id}', [ActivityController::class, 'update'])->name('activities.update');
    Route::delete('/activities/{id}', [ActivityController::class, 'destroy'])->name('activities.destroy');


    Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/categories/add', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories/store', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});



























