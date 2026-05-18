@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="card">
    <h2>Tableau de bord</h2>
    <p>Bienvenue {{ auth()->user()->prenom }} {{ auth()->user()->nom }}.</p>
</div>

<div class="card">
    <h2>Accès rapide</h2>
    <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-top:1rem">
        <a href="{{ route('restaurants.index') }}" class="btn">Restaurants</a>
        <a href="{{ route('collaborateurs.index') }}" class="btn">Collaborateurs</a>
        <a href="{{ route('fonctions.index') }}" class="btn">Fonctions</a>
        <a href="{{ route('affectations.index') }}" class="btn">Affectations</a>
        <a href="{{ route('affectations.create') }}" class="btn btn-secondary">+ Nouvelle affectation</a>
        <a href="{{ route('collaborateurs.index', ['non_affecte' => 1]) }}" class="btn btn-secondary">Collaborateurs non affectés</a>
    </div>
</div>
@endsection
