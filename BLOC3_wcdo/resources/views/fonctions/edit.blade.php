@extends('layouts.app')
@section('title', 'Modifier la fonction')

@section('content')
<div class="card" style="max-width:520px">
    <h2>Modifier la fonction</h2>

    <form method="POST" action="{{ route('fonctions.update', $fonction) }}">
        @csrf
        @method('PUT')
        @include('fonctions._form', ['fonction' => $fonction])
        <div class="actions">
            <button type="submit" class="btn">Enregistrer</button>
            <a href="{{ route('fonctions.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
@endsection
