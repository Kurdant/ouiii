<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RestaurantController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['nom', 'code_postal', 'ville']);

        $restaurants = Restaurant::query()
            ->rechercher($filters)
            ->orderBy('nom')
            ->paginate(15)
            ->withQueryString();

        return view('restaurants.index', compact('restaurants', 'filters'));
    }

    public function create(): View
    {
        return view('restaurants.create');
    }

    public function store(StoreRestaurantRequest $request): RedirectResponse
    {
        $restaurant = Restaurant::create($request->validated());

        return redirect()
            ->route('restaurants.show', $restaurant)
            ->with('success', 'Restaurant créé.');
    }

    public function show(Restaurant $restaurant): View
    {
        // CDC §3.5 : fiche restaurant = en cours + historique.
        $enCours = $restaurant->affectations()
            ->enCours()
            ->with(['collaborateur', 'fonction'])
            ->orderBy('date_debut')
            ->get();

        $historique = $restaurant->affectations()
            ->terminees()
            ->with(['collaborateur', 'fonction'])
            ->orderByDesc('date_fin')
            ->get();

        return view('restaurants.show', compact('restaurant', 'enCours', 'historique'));
    }

    public function edit(Restaurant $restaurant): View
    {
        return view('restaurants.edit', compact('restaurant'));
    }

    public function update(UpdateRestaurantRequest $request, Restaurant $restaurant): RedirectResponse
    {
        $restaurant->update($request->validated());

        return redirect()
            ->route('restaurants.show', $restaurant)
            ->with('success', 'Restaurant modifié.');
    }
}
