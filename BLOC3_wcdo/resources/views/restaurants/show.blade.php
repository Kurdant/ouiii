@extends('layouts.app')
@section('title', $restaurant->nom)

@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h2>{{ $restaurant->nom }}</h2>
        <div>
            <a href="{{ route('restaurants.edit', $restaurant) }}" class="btn">Modifier</a>
            <a href="{{ route('restaurants.index') }}" class="btn btn-secondary">Retour</a>
        </div>
    </div>
    <p style="margin-top:1rem">
        <strong>Adresse :</strong> {{ $restaurant->adresse }}<br>
        <strong>Code postal :</strong> {{ $restaurant->code_postal }}<br>
        <strong>Ville :</strong> {{ $restaurant->ville }}
    </p>
</div>

<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h2>Affectations en cours</h2>
        <a href="{{ route('affectations.create', ['restaurant_id' => $restaurant->id]) }}" class="btn">+ Affecter un collaborateur</a>
    </div>
    @if ($enCours->isNotEmpty())
        <table>
            <thead><tr><th>Collaborateur</th><th>Fonction</th><th>Depuis le</th><th>Jusqu'au</th><th>Actions</th></tr></thead>
            <tbody>
                @foreach ($enCours as $a)
                    <tr>
                        <td>
                            <a href="{{ route('collaborateurs.show', $a->collaborateur_id) }}">
                                {{ $a->collaborateur->prenom }} {{ $a->collaborateur->nom }}
                            </a>
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
        <p style="color:#888;margin-top:1rem">Aucun collaborateur actuellement en poste dans ce restaurant.</p>
    @endif
</div>

<div class="card">
    <h2>Historique</h2>
    @if ($historique->isNotEmpty())
        <table>
            <thead><tr><th>Collaborateur</th><th>Fonction</th><th>Du</th><th>Au</th></tr></thead>
            <tbody>
                @foreach ($historique as $a)
                    <tr>
                        <td>
                            <a href="{{ route('collaborateurs.show', $a->collaborateur_id) }}">
                                {{ $a->collaborateur->prenom }} {{ $a->collaborateur->nom }}
                            </a>
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
