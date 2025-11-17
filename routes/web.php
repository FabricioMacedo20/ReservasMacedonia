<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\AuthController;

// Rota padrão - Redireciona para o mapa
Route::get('/', function () {
    return redirect()->route('mesas.map');
});

// Rotas de autenticação
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas de reservas
Route::get('/reservas', [ReservationController::class, 'index'])->name('reservas.index');
Route::get('/reservas/criar', [ReservationController::class, 'create'])->name('reservas.create');
Route::post('/reservas', [ReservationController::class, 'store'])->name('reservas.store');
Route::get('/reservas/{id}', [ReservationController::class, 'show'])->name('reservas.show');
Route::post('/reservas/{id}/cancelar', [ReservationController::class, 'cancel'])->name('reservas.cancel');

// Rotas de mesas
Route::get('/mesas', [TableController::class, 'index'])->name('mesas.index');
Route::get('/mesas/{id}', [TableController::class, 'show'])->name('mesas.show');
Route::get('/mesas/disponiveis', [TableController::class, 'available'])->name('mesas.available');

// Rota do mapa de mesas
Route::get('/mapa', [TableController::class, 'map'])->name('mesas.map');
