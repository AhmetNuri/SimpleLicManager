<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LicenseController;
use App\Http\Controllers\Admin\LicLogController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [ProfileController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [ProfileController::class, 'register'])->name('register.post');
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::post('/login', [ProfileController::class, 'login'])->name('login.post');
});

Route::post('/logout', [ProfileController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// User Dashboard Routes
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.users.index');
    })->name('index');
    
    Route::resource('users', UserController::class);
    Route::resource('licenses', LicenseController::class);
    Route::get('logs', [LicLogController::class, 'index'])->name('logs.index');
});

