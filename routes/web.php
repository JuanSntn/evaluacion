<?php

use App\Http\Controllers\AccountPasswordController;
use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\Auth\AlgorithmController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\CollaboratorController;
use App\Http\Controllers\Auth\PasswordRecoveryController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\ManagedUserController;
use App\Http\Controllers\Auth\ServicePostController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.store');

    Route::get('/forgot-password', [PasswordRecoveryController::class, 'create'])
        ->name('password.forgot');
    Route::post('/forgot-password', [PasswordRecoveryController::class, 'store'])
        ->middleware('throttle:password-recovery')
        ->name('password.recover');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // rutas para la configuracion de la cuenta
    Route::get('/account', [AccountSettingsController::class, 'edit'])->name('account.edit');
    Route::put('/account', [AccountSettingsController::class, 'update'])->name('account.update');
    Route::put('/account/password', AccountPasswordController::class)->name('account.password.update');

    // rutas de colaboradores
    Route::resource('collaborators', CollaboratorController::class);

    // Pantallas de interfaz para Usuarios y Servicios. La persistencia se agregará después.
    Route::get('/users-and-services', [ManagedUserController::class, 'index'])->name('users-and-services.index');
    Route::resource('users-and-services/users', ManagedUserController::class)->except(['index'])->names('users-and-services.users');

    // rutas para la funcionalidad de algoritmos
    Route::get('/algorithm', [AlgorithmController::class, 'index'])->name('algorithm.index');
    Route::post('/algorithm', [AlgorithmController::class, 'palindromes'])->name('algorithm.palindromes');

    // rutas para  para la funcionalidad de publicaciones de servicios
    Route::prefix('users-and-services/services')->name('users-and-services.services.')->controller(ServicePostController::class)->group(function (): void {
        Route::get('/posts', 'index')->name('posts.index');
        Route::post('/posts', 'store')->name('posts.store');
        Route::put('/posts/{post}', 'update')->whereNumber('post')->name('posts.update');
        Route::delete('/posts/{post}', 'destroy')->whereNumber('post')->name('posts.destroy');
    });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
