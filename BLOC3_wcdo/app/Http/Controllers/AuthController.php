<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Contrôleur d'authentification.
 *
 * CDC : seul un collaborateur avec `administrateur = true` accède au back-office.
 * La vérification du flag est portée par le middleware `EnsureUserIsAdmin`
 * appliqué sur les routes métier ; ici on se contente de valider les
 * identifiants et d'établir la session.
 *
 * Mesures OWASP appliquées :
 * - Message d'erreur générique (anti énumération de comptes).
 * - `regenerate()` après authentification (anti session-fixation).
 * - `invalidate()` + `regenerateToken()` au logout.
 * - CSRF natif Laravel sur les formulaires POST.
 */
class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials, false)) {
            throw ValidationException::withMessages([
                'email' => 'Identifiants invalides.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
