<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoorController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Ruta para la acción de abrir puerta (POST para evitar CSRF si se usa un botón)
Route::post('/door/open', [DoorController::class, 'open'])->name('door.open');

use App\Livewire\GestionPersonal;

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rutas de Administrador
    Route::middleware('admin')->group(function () {
        Route::get('/personal', GestionPersonal::class)->name('personal.index');
    });
});

require __DIR__.'/auth.php';
