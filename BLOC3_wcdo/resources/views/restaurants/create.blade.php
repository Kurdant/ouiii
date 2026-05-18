@extends('layouts.app')
@section('title', 'Nouveau restaurant')

@section('content')
<div class="card" style="max-width:640px">
    <h2>Nouveau restaurant</h2>
    <form method="POST" action="{{ route('restaurants.store') }}">
        @csrf
        @include('restaurants._form', ['restaurant' => null])
        <div class="actions">
            <button type="submit" class="btn">Créer</button>
            <a href="{{ route('restaurants.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
@endsection
