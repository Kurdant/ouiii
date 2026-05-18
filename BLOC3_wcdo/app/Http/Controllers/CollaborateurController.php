<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCollaborateurRequest;
use App\Http\Requests\UpdateCollaborateurRequest;
use App\Models\Collaborateur;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CollaborateurController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $nonAffecte = $request->boolean('non_affecte');

        $collaborateurs = Collaborateur::query()
            ->when($q !== '', function ($query) use ($q) {
                $like = '%'.$q.'%';
                $query->where(function ($sub) use ($like) {
                    $sub->where('nom', 'ilike', $like)
                        ->orWhere('prenom', 'ilike', $like)
                        ->orWhere('email', 'ilike', $like);
                });
            })
            ->when($nonAffecte, fn ($query) => $query->nonAffectes())
            ->orderBy('nom')
            ->orderBy('prenom')
            ->paginate(15)
            ->withQueryString();

        return view('collaborateurs.index', compact('collaborateurs', 'q', 'nonAffecte'));
    }

    public function create(): View
    {
        return view('collaborateurs.create');
    }

    public function store(StoreCollaborateurRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // password est nullable ; si renseigné, le cast `hashed` le hachera automatiquement.
        if (empty($data['password'])) {
            $data['password'] = null;
        }

        $collaborateur = Collaborateur::create($data);

        return redirect()
            ->route('collaborateurs.show', $collaborateur)
            ->with('success', 'Collaborateur créé.');
    }

    public function show(Collaborateur $collaborateur): View
    {
        $enCours = $collaborateur->affectations()
            ->enCours()
            ->with(['restaurant', 'fonction'])
            ->orderBy('date_debut')
            ->get();

        $historique = $collaborateur->affectations()
            ->terminees()
            ->with(['restaurant', 'fonction'])
            ->orderByDesc('date_fin')
            ->get();

        return view('collaborateurs.show', compact('collaborateur', 'enCours', 'historique'));
    }

    public function edit(Collaborateur $collaborateur): View
    {
        return view('collaborateurs.edit', compact('collaborateur'));
    }

    public function update(UpdateCollaborateurRequest $request, Collaborateur $collaborateur): RedirectResponse
    {
        $data = $request->validated();

        // En modification, un mot de passe vide signifie "inchangé".
        if (empty($data['password'])) {
            unset($data['password']);
        }

        // CDC : un administrateur doit avoir un mot de passe.
        $becomesAdmin = ($data['administrateur'] ?? false) === true;
        $hasNoPasswordYet = ! array_key_exists('password', $data) && empty($collaborateur->password);

        if ($becomesAdmin && $hasNoPasswordYet) {
            return back()
                ->withInput($request->except('password'))
                ->withErrors(['password' => 'Un mot de passe est obligatoire pour un administrateur.']);
        }

        $collaborateur->update($data);

        return redirect()
            ->route('collaborateurs.show', $collaborateur)
            ->with('success', 'Collaborateur modifié.');
    }
}
