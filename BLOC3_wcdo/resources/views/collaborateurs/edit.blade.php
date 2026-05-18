@extends('layouts.app')
@section('title', 'Modifier collaborateur')

@section('content')
<div class="card" style="max-width:720px">
    <h2>Modifier le collaborateur</h2>
    <form method="POST" action="{{ route('collaborateurs.update', $collaborateur) }}">
        @csrf
        @method('PUT')
        @include('collaborateurs._form', ['collaborateur' => $collaborateur, 'isEdit' => true])
        <div class="actions">
            <button type="submit" class="btn">Enregistrer</button>
            <a href="{{ route('collaborateurs.show', $collaborateur) }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
@endsection
