@extends('layouts.app')
@section('title', 'Nouveau collaborateur')

@section('content')
<div class="card" style="max-width:720px">
    <h2>Nouveau collaborateur</h2>
    <form method="POST" action="{{ route('collaborateurs.store') }}">
        @csrf
        @include('collaborateurs._form', ['collaborateur' => null, 'isEdit' => false])
        <div class="actions">
            <button type="submit" class="btn">Créer</button>
            <a href="{{ route('collaborateurs.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
@endsection
