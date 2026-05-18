<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware : exige que l'utilisateur connecté soit administrateur.
 *
 * CDC : l'accès applicatif est porté par le booléen `administrateur` du
 * collaborateur. Aucun collaborateur non administrateur ne doit franchir
 * cette barrière, même authentifié.
 *
 * Non authentifié -> redirection /login.
 * Authentifié mais non admin -> logout immédiat + 403.
 */
class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return redirect()->guest('/login');
        }

        if (! $user->administrateur) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            abort(403, 'Accès réservé aux administrateurs.');
        }

        return $next($request);
    }
}
