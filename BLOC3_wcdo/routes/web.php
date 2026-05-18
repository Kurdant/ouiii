<?php

use App\Http\Controllers\AffectationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CollaborateurController;
use App\Http\Controllers\FonctionController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes web - Wacdo
|--------------------------------------------------------------------------
|
| Sprints 0-3 actifs :
|   - / et /login : public
|   - /logout : auth
|   - back-office (auth + admin) : /dashboard, fonctions, restaurants, collaborateurs
|
| Sprint 4 ajoutera les routes affectations, Sprint 5 les recherches dédiées.
|
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
})->name('home');

// Authentification (public)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.attempt');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Back-office (réservé administrateurs)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Fonctions : pas de suppression (CDC), pas de show (peu utile).
    Route::resource('fonctions', FonctionController::class)
        ->except(['show', 'destroy']);

    // Restaurants : pas de suppression (CDC + FK RESTRICT).
    Route::resource('restaurants', RestaurantController::class)
        ->except(['destroy']);

    // Collaborateurs : pas de suppression (CDC + FK RESTRICT).
    Route::resource('collaborateurs', CollaborateurController::class)
        ->except(['destroy']);

    // Affectations : création (depuis dashboard, collaborateur ou restaurant)
    // et modification uniquement. Aucune suppression : c'est l'historique.
    Route::get('/affectations', [AffectationController::class, 'index'])
        ->name('affectations.index');
    Route::get('/affectations/create', [AffectationController::class, 'create'])
        ->name('affectations.create');
    Route::post('/affectations', [AffectationController::class, 'store'])
        ->name('affectations.store');
    Route::get('/affectations/{affectation}/edit', [AffectationController::class, 'edit'])
        ->name('affectations.edit');
    Route::put('/affectations/{affectation}', [AffectationController::class, 'update'])
        ->name('affectations.update');

    // Sprint 5 : recherches avancées.
});
