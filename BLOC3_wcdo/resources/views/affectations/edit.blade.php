@extends('layouts.app')
@section('title', 'Modifier affectation')

@section('content')
<div class="card" style="max-width:720px">
    <h2>Modifier l'affectation</h2>
    <form method="POST" action="{{ route('affectations.update', $affectation) }}">
        @csrf
        @method('PUT')
        @include('affectations._form', ['affectation' => $affectation])
        <div class="actions">
            <button type="submit" class="btn">Enregistrer</button>
            <a href="{{ route('collaborateurs.show', $affectation->collaborateur_id) }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
@endsection
