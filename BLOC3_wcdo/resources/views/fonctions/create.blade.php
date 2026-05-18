@extends('layouts.app')
@section('title', 'Nouvelle fonction')

@section('content')
<div class="card" style="max-width:520px">
    <h2>Nouvelle fonction</h2>

    <form method="POST" action="{{ route('fonctions.store') }}">
        @csrf
        @include('fonctions._form', ['fonction' => null])
        <div class="actions">
            <button type="submit" class="btn">Créer</button>
            <a href="{{ route('fonctions.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
@endsection
