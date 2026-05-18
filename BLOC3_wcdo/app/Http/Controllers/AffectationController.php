<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAffectationRequest;
use App\Http\Requests\UpdateAffectationRequest;
use App\Models\Affectation;
use App\Models\Collaborateur;
use App\Models\Fonction;
use App\Models\Restaurant;
use App\Services\AffectationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AffectationController extends Controller
{
    public function __construct(private readonly AffectationService $service)
    {
    }

    /**
     * CDC \u00a73.5 et \u00a77.9 : recherche transversale des affectations.
     * Filtres : collaborateur, restaurant, fonction, ville, nom, dates.
     * Statut affichable : en cours / futures / termin\u00e9es / toutes.
     */
    public function index(Request $request): View
    {
        $filters = [
            'collaborateur_id' => $request->integer('collaborateur_id') ?: null,
            'restaurant_id'    => $request->integer('restaurant_id') ?: null,
            'fonction_id'      => $request->integer('fonction_id') ?: null,
            'nom'              => trim((string) $request->query('nom', '')) ?: null,
            'ville'            => trim((string) $request->query('ville', '')) ?: null,
            'date_debut'       => $request->query('date_debut') ?: null,
            'date_fin'         => $request->query('date_fin') ?: null,
        ];
        $statut = $request->query('statut'); // 'en_cours' | 'futures' | 'terminees' | null

        $query = Affectation::query()
            ->with(['collaborateur', 'restaurant', 'fonction'])
            ->filtrer($filters);

        $query = match ($statut) {
            'en_cours'  => $query->enCours(),
            'futures'   => $query->futures(),
            'terminees' => $query->terminees(),
            default     => $query,
        };

        $affectations = $query
            ->orderByDesc('date_debut')
            ->paginate(15)
            ->withQueryString();

        return view('affectations.index', [
            'affectations'   => $affectations,
            'filters'        => $filters,
            'statut'         => $statut,
            'collaborateurs' => Collaborateur::orderBy('nom')->orderBy('prenom')->get(),
            'restaurants'    => Restaurant::orderBy('nom')->get(),
            'fonctions'      => Fonction::ordonnerParIntitule()->get(),
        ]);
    }

    /**
     * Pr\u00e9-remplit depuis une fiche collaborateur ou restaurant via query string.
     */
    public function create(Request $request): View
    {
        $prefill = [
            'collaborateur_id' => $request->integer('collaborateur_id') ?: null,
            'restaurant_id'    => $request->integer('restaurant_id') ?: null,
            'fonction_id'      => null,
            'date_debut'       => null,
            'date_fin'         => null,
        ];

        return view('affectations.create', [
            'prefill'        => $prefill,
            'collaborateurs' => Collaborateur::orderBy('nom')->orderBy('prenom')->get(),
            'restaurants'    => Restaurant::orderBy('nom')->get(),
            'fonctions'      => Fonction::ordonnerParIntitule()->get(),
        ]);
    }

    public function store(StoreAffectationRequest $request): RedirectResponse
    {
        $affectation = $this->service->create($request->validated());

        return redirect()
            ->route('collaborateurs.show', $affectation->collaborateur_id)
            ->with('success', 'Affectation créée.');
    }

    public function edit(Affectation $affectation): View
    {
        return view('affectations.edit', [
            'affectation'    => $affectation,
            'collaborateurs' => Collaborateur::orderBy('nom')->orderBy('prenom')->get(),
            'restaurants'    => Restaurant::orderBy('nom')->get(),
            'fonctions'      => Fonction::ordonnerParIntitule()->get(),
        ]);
    }

    public function update(UpdateAffectationRequest $request, Affectation $affectation): RedirectResponse
    {
        $affectation = $this->service->update($affectation, $request->validated());

        return redirect()
            ->route('collaborateurs.show', $affectation->collaborateur_id)
            ->with('success', 'Affectation modifiée.');
    }
}
