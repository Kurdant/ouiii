@extends('layouts.app')
@section('title', 'Recherche d\'affectations')

@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h2>Recherche d'affectations</h2>
        <a href="{{ route('affectations.create') }}" class="btn">+ Nouvelle affectation</a>
    </div>

    <form method="GET" action="{{ route('affectations.index') }}" class="filters" style="margin-top:1rem;display:grid;grid-template-columns:repeat(4,1fr);gap:0.75rem">
        <div class="form-group">
            <label for="collaborateur_id">Collaborateur</label>
            <select id="collaborateur_id" name="collaborateur_id">
                <option value="">— Tous —</option>
                @foreach ($collaborateurs as $c)
                    <option value="{{ $c->id }}" @selected(($filters['collaborateur_id'] ?? null) == $c->id)>
                        {{ $c->nom }} {{ $c->prenom }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="restaurant_id">Restaurant</label>
            <select id="restaurant_id" name="restaurant_id">
                <option value="">— Tous —</option>
                @foreach ($restaurants as $r)
                    <option value="{{ $r->id }}" @selected(($filters['restaurant_id'] ?? null) == $r->id)>
                        {{ $r->nom }} ({{ $r->ville }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="fonction_id">Fonction</label>
            <select id="fonction_id" name="fonction_id">
                <option value="">— Toutes —</option>
                @foreach ($fonctions as $f)
                    <option value="{{ $f->id }}" @selected(($filters['fonction_id'] ?? null) == $f->id)>
                        {{ $f->intitule_poste }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="statut">Statut</label>
            <select id="statut" name="statut">
                <option value="">— Tous —</option>
                <option value="en_cours" @selected($statut === 'en_cours')>En cours</option>
                <option value="futures" @selected($statut === 'futures')>Futures</option>
                <option value="terminees" @selected($statut === 'terminees')>Terminées</option>
            </select>
        </div>
        <div class="form-group">
            <label for="nom">Nom / prénom collaborateur</label>
            <input type="text" id="nom" name="nom" value="{{ $filters['nom'] }}">
        </div>
        <div class="form-group">
            <label for="ville">Ville restaurant</label>
            <input type="text" id="ville" name="ville" value="{{ $filters['ville'] }}">
        </div>
        <div class="form-group">
            <label for="date_debut">Date début ≥</label>
            <input type="date" id="date_debut" name="date_debut" value="{{ $filters['date_debut'] }}">
        </div>
        <div class="form-group">
            <label for="date_fin">Date début ≤</label>
            <input type="date" id="date_fin" name="date_fin" value="{{ $filters['date_fin'] }}">
        </div>
        <div class="actions" style="grid-column:span 4">
            <button type="submit" class="btn">Filtrer</button>
            <a href="{{ route('affectations.index') }}" class="btn btn-secondary">Réinitialiser</a>
        </div>
    </form>

    <table style="margin-top:1.5rem">
        <thead>
            <tr>
                <th>Collaborateur</th>
                <th>Restaurant</th>
                <th>Fonction</th>
                <th>Du</th>
                <th>Au</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($affectations as $a)
                <tr>
                    <td>
                        <a href="{{ route('collaborateurs.show', $a->collaborateur_id) }}">
                            {{ $a->collaborateur->nom }} {{ $a->collaborateur->prenom }}
                        </a>
                    </td>
                    <td>
                        <a href="{{ route('restaurants.show', $a->restaurant_id) }}">
                            {{ $a->restaurant->nom }}
                        </a>
                        <span style="color:#888"> — {{ $a->restaurant->ville }}</span>
                    </td>
                    <td>{{ $a->fonction->intitule_poste }}</td>
                    <td>{{ $a->date_debut->format('d/m/Y') }}</td>
                    <td>{{ $a->date_fin?->format('d/m/Y') ?? '—' }}</td>
                    <td><a href="{{ route('affectations.edit', $a) }}" class="btn btn-secondary">Modifier</a></td>
                </tr>
            @empty
                <tr><td colspan="6" style="color:#888;text-align:center">Aucune affectation ne correspond aux critères.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:1rem">{{ $affectations->links() }}</div>
</div>
@endsection
