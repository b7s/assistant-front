<?php

use App\Http\Controllers\LogoutController;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function (): void {
    Route::get('login', Login::class)->name('login');
    Route::get('register', Register::class)->name('register');
});

Route::middleware('auth')->group(function (): void {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::post('logout', LogoutController::class)->name('logout');
});

require __DIR__.'/settings.php';
