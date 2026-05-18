@extends('layouts.app')
@section('title', 'Nouvelle affectation')

@section('content')
<div class="card" style="max-width:720px">
    <h2>Nouvelle affectation</h2>
    <form method="POST" action="{{ route('affectations.store') }}">
        @csrf
        @include('affectations._form', ['affectation' => null])
        <div class="actions">
            <button type="submit" class="btn">Créer</button>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
@endsection
