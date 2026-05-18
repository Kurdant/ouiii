@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
<div class="card" style="max-width: 420px; margin: 3rem auto;">
    <h2>Connexion administrateur</h2>

    @if ($errors->any())
        <div class="alert alert-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ url('/login') }}" novalidate>
        @csrf

        <div class="form-group">
            <label for="email">Adresse email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
        </div>

        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">
        </div>

        <div class="actions">
            <button type="submit" class="btn">Se connecter</button>
        </div>
    </form>
</div>
@endsection

