@extends('layouts.app')
@section('title', $collaborateur->prenom . ' ' . $collaborateur->nom)

@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h2>{{ $collaborateur->prenom }} {{ $collaborateur->nom }}</h2>
        <div>
            <a href="{{ route('collaborateurs.edit', $collaborateur) }}" class="btn">Modifier</a>
            <a href="{{ route('collaborateurs.index') }}" class="btn btn-secondary">Retour</a>
        </div>
    </div>
    <p style="margin-top:1rem">
        <strong>Email :</strong> {{ $collaborateur->email }}<br>
        <strong>Téléphone :</strong> {{ $collaborateur->telephone ?? '—' }}<br>
        <strong>Date de première embauche :</strong> {{ $collaborateur->date_premiere_embauche->format('d/m/Y') }}<br>
        <strong>Administrateur :</strong> {{ $collaborateur->administrateur ? 'Oui' : 'Non' }}
    </p>
</div>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h2>Affectations en cours</h2>
        <a href="{{ route('affectations.create', ['collaborateur_id' => $collaborateur->id]) }}" class="btn">+ Nouvelle affectation</a>
    </div>
    @if ($enCours->isNotEmpty())
        <table>
            <thead><tr><th>Restaurant</th><th>Fonction</th><th>Depuis le</th><th>Jusqu'au</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach ($enCours as $a)
                    <tr>
                        <td>
                            <a href="{{ route('restaurants.show', $a->restaurant_id) }}">{{ $a->restaurant->nom }}</a>
                            <span style="color:#888"> — {{ $a->restaurant->ville }}</span>
                        </td>
                        <td>{{ $a->fonction->intitule_poste }}</td>
                        <td>{{ $a->date_debut->format('d/m/Y') }}</td>
                        <td>{{ $a->date_fin?->format('d/m/Y') ?? 'non bornée' }}</td>
                        <td><a href="{{ route('affectations.edit', $a) }}" class="btn btn-secondary">Modifier</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color:#888;margin-top:1rem">Ce collaborateur n'est actuellement affecté à aucun restaurant.</p>
    @endif
</div>

<div class="card">
    <h2>Historique</h2>
    @if ($historique->isNotEmpty())
        <table>
            <thead><tr><th>Restaurant</th><th>Fonction</th><th>Du</th><th>Au</th></tr></thead>
            <tbody>
                @foreach ($historique as $a)
                    <tr>
                        <td>
                            <a href="{{ route('restaurants.show', $a->restaurant_id) }}">{{ $a->restaurant->nom }}</a>
                        </td>
                        <td>{{ $a->fonction->intitule_poste }}</td>
                        <td>{{ $a->date_debut->format('d/m/Y') }}</td>
                        <td>{{ $a->date_fin->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p style="color:#888;margin-top:1rem">Aucune affectation terminée enregistrée.</p>
    @endif
</div>
@endsection
