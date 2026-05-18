@extends('layouts.app')
@section('title', 'Collaborateurs')

@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h2>Collaborateurs</h2>
        <a href="{{ route('collaborateurs.create') }}" class="btn">+ Nouveau collaborateur</a>
    </div>

    <form method="GET" action="{{ route('collaborateurs.index') }}" style="margin-top:1rem">
        <div class="filters">
            <div class="form-group">
                <label for="q">Recherche (nom, prénom ou email)</label>
                <input type="text" id="q" name="q" value="{{ $q }}">
            </div>
            <div class="form-group">
                <label style="display:flex;align-items:center;gap:0.5rem;margin-top:1.5rem">
                    <input type="checkbox" name="non_affecte" value="1" @checked($nonAffecte)>
                    Uniquement les collaborateurs non affectés aujourd'hui
                </label>
            </div>
            <div class="form-group">
                <button type="submit" class="btn">Filtrer</button>
                <a href="{{ route('collaborateurs.index') }}" class="btn btn-secondary">Réinitialiser</a>
            </div>
        </div>
    </form>

    <table>
        <thead>
            <tr><th>Nom</th><th>Prénom</th><th>Email</th><th>Admin ?</th><th style="width:120px">Actions</th></tr>
        </thead>
        <tbody>
            @forelse ($collaborateurs as $c)
                <tr>
                    <td>{{ $c->nom }}</td>
                    <td>{{ $c->prenom }}</td>
                    <td>{{ $c->email }}</td>
                    <td>{{ $c->administrateur ? 'Oui' : 'Non' }}</td>
                    <td><a href="{{ route('collaborateurs.show', $c) }}" class="btn btn-secondary">Voir</a></td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;color:#888;padding:2rem">Aucun collaborateur trouvé.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:1rem">{{ $collaborateurs->links() }}</div>
</div>
@endsection
