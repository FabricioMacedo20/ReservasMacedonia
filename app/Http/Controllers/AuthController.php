<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    // Mostra a tela de login

    public function login(): View
    {
        return view('auth.login');
    }

    //Autentica o usuário (simplificado para demo)
    public function authenticate(Request $request): RedirectResponse
    {
        // Para esta demo, qualquer visitante pode acessar
        return redirect()->route('mesas.map');
    }

    // Faz logout do usuário
    public function logout(): RedirectResponse
    {
        return redirect()->route('mesas.map');
    }
}