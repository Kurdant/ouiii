<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFonctionRequest;
use App\Http\Requests\UpdateFonctionRequest;
use App\Models\Fonction;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FonctionController extends Controller
{
    public function index(): View
    {
        $fonctions = Fonction::query()
            ->orderBy('intitule_poste')
            ->paginate(15);

        return view('fonctions.index', compact('fonctions'));
    }

    public function create(): View
    {
        return view('fonctions.create');
    }

    public function store(StoreFonctionRequest $request): RedirectResponse
    {
        Fonction::create($request->validated());

        return redirect()
            ->route('fonctions.index')
            ->with('success', 'Fonction créée.');
    }

    public function edit(Fonction $fonction): View
    {
        return view('fonctions.edit', compact('fonction'));
    }

    public function update(UpdateFonctionRequest $request, Fonction $fonction): RedirectResponse
    {
        $fonction->update($request->validated());

        return redirect()
            ->route('fonctions.index')
            ->with('success', 'Fonction modifiée.');
    }
}
