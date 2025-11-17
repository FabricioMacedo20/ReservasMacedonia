<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\AuthController;

/**
 * DEFINIÇÃO DE ROTAS - Sistema de Reservas de Mesas
 * 
 * Padrão: RESTful
 * Prefixos:
 * - /mapa - Gerenciamento de mesas
 * - /reservas - Gerenciamento de reservas
 * - /login - Autenticação
 */

// ============ ROTA PADRÃO ============
// Redireciona usuários da raiz (/) para o mapa principal
Route::get('/', function () {
    return redirect()->route('mesas.map');
});

// ============ ROTAS DE AUTENTICAÇÃO ============
// GET: Exibe formulário de login
Route::get('/login', [AuthController::class, 'login'])->name('login');

// POST: Autentica usuário
Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');

// POST: Desautentica usuário (logout)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ============ ROTAS DE RESERVAS ============
// GET: Lista todas as reservas ativas
Route::get('/reservas', [ReservationController::class, 'index'])->name('reservas.index');

// GET: Exibe formulário para criar reserva (não utilizado no fluxo atual)
Route::get('/reservas/criar', [ReservationController::class, 'create'])->name('reservas.create');

// POST: Cria nova reserva (chamado pelo modal)
Route::post('/reservas', [ReservationController::class, 'store'])->name('reservas.store');

// GET: Exibe detalhes de uma reserva
Route::get('/reservas/{id}', [ReservationController::class, 'show'])->name('reservas.show');

// POST: Cancela uma reserva existente
Route::post('/reservas/{id}/cancelar', [ReservationController::class, 'cancel'])->name('reservas.cancel');

// ============ ROTAS DE MESAS ============
// GET: Lista todas as mesas (não utilizado na interface)
Route::get('/mesas', [TableController::class, 'index'])->name('mesas.index');

// GET: Retorna horários de uma mesa específica via AJAX
// Chamado quando usuário clica em uma mesa no mapa
Route::get('/mesas/{id}', [TableController::class, 'show'])->name('mesas.show');

// GET: Retorna mesas com time slots em JSON (para APIs externas)
Route::get('/mesas/disponiveis', [TableController::class, 'available'])->name('mesas.available');

// ============ ROTA PRINCIPAL DO MAPA ============
// GET: Exibe o mapa com 80 mesas
// Rota mais importante: ponto de entrada principal da aplicação
Route::get('/mapa', [TableController::class, 'map'])->name('mesas.map');
