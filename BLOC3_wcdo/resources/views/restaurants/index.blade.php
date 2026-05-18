@extends('layouts.app')
@section('title', 'Restaurants')

@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h2>Restaurants</h2>
        <a href="{{ route('restaurants.create') }}" class="btn">+ Nouveau restaurant</a>
    </div>

    <form method="GET" action="{{ route('restaurants.index') }}" style="margin-top:1rem">
        <div class="filters">
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" value="{{ $filters['nom'] ?? '' }}">
            </div>
            <div class="form-group">
                <label for="code_postal">Code postal</label>
                <input type="text" id="code_postal" name="code_postal" value="{{ $filters['code_postal'] ?? '' }}">
            </div>
            <div class="form-group">
                <label for="ville">Ville</label>
                <input type="text" id="ville" name="ville" value="{{ $filters['ville'] ?? '' }}">
            </div>
            <div class="form-group">
                <button type="submit" class="btn">Rechercher</button>
                <a href="{{ route('restaurants.index') }}" class="btn btn-secondary">Réinitialiser</a>
            </div>
        </div>
    </form>

    <table>
        <thead>
            <tr><th>Nom</th><th>Adresse</th><th>CP</th><th>Ville</th><th style="width:160px">Actions</th></tr>
        </thead>
        <tbody>
            @forelse ($restaurants as $r)
                <tr>
                    <td>{{ $r->nom }}</td>
                    <td>{{ $r->adresse }}</td>
                    <td>{{ $r->code_postal }}</td>
                    <td>{{ $r->ville }}</td>
                    <td>
                        <a href="{{ route('restaurants.show', $r) }}" class="btn btn-secondary">Voir</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;color:#888;padding:2rem">Aucun restaurant trouvé.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:1rem">{{ $restaurants->links() }}</div>
</div>
@endsection
