@extends('layouts.app')
@section('title', 'Modifier restaurant')

@section('content')
<div class="card" style="max-width:640px">
    <h2>Modifier le restaurant</h2>
    <form method="POST" action="{{ route('restaurants.update', $restaurant) }}">
        @csrf
        @method('PUT')
        @include('restaurants._form', ['restaurant' => $restaurant])
        <div class="actions">
            <button type="submit" class="btn">Enregistrer</button>
            <a href="{{ route('restaurants.show', $restaurant) }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
@endsection
