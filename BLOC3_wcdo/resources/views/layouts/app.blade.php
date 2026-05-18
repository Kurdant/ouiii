<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Wacdo - Gestion des affectations')</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f5f5f5; color: #222; }
        header { background: #ffcc00; color: #222; padding: 1rem 2rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,.1); }
        header h1 { font-size: 1.4rem; }
        header nav a { color: #222; text-decoration: none; margin-left: 1rem; font-weight: 500; }
        header nav a:hover { text-decoration: underline; }
        header form { display: inline; }
        header button { background: none; border: 1px solid #222; color: #222; padding: .4rem .8rem; cursor: pointer; font-size: .9rem; border-radius: 4px; }
        header button:hover { background: #222; color: #ffcc00; }
        main { max-width: 1100px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: #fff; padding: 1.5rem; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,.08); margin-bottom: 1.5rem; }
        h2 { margin-bottom: 1rem; color: #333; }
        .alert { padding: .8rem 1rem; border-radius: 4px; margin-bottom: 1rem; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: .6rem .8rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #fafafa; font-weight: 600; }
        tr:hover { background: #fafafa; }
        .btn { display: inline-block; padding: .5rem 1rem; background: #ffcc00; color: #222; border: none; border-radius: 4px; text-decoration: none; font-weight: 500; cursor: pointer; font-size: .95rem; }
        .btn:hover { background: #e6b800; }
        .btn-secondary { background: #6c757d; color: #fff; }
        .btn-secondary:hover { background: #5a6268; }
        .btn-danger { background: #dc3545; color: #fff; }
        .btn-danger:hover { background: #c82333; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: .3rem; font-weight: 500; }
        input[type=text], input[type=email], input[type=password], input[type=date], input[type=tel], select, textarea {
            width: 100%; padding: .5rem .7rem; border: 1px solid #ccc; border-radius: 4px; font-size: 1rem;
        }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #ffcc00; box-shadow: 0 0 0 2px rgba(255,204,0,.2); }
        .error-text { color: #dc3545; font-size: .85rem; margin-top: .3rem; }
        .actions { margin-top: 1.5rem; display: flex; gap: .5rem; }
        .filters { display: flex; gap: 1rem; flex-wrap: wrap; align-items: end; }
        .filters .form-group { flex: 1; min-width: 180px; margin-bottom: 0; }
    </style>
</head>
<body>
    <header>
        <h1>Wacdo - Gestion des affectations</h1>
        <nav>
            @auth
                <a href="{{ route('dashboard') }}">Accueil</a>
                <a href="{{ route('restaurants.index') }}">Restaurants</a>
                <a href="{{ route('collaborateurs.index') }}">Collaborateurs</a>
                <a href="{{ route('fonctions.index') }}">Fonctions</a>
                <a href="{{ route('affectations.index') }}">Affectations</a>
                <span style="margin-left:1rem">{{ auth()->user()->prenom }} {{ auth()->user()->nom }}</span>
                <form method="POST" action="{{ url('/logout') }}" style="display:inline">
                    @csrf
                    <button type="submit">Se déconnecter</button>
                </form>
            @endauth
        </nav>
    </header>
    <main>
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if (session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        @yield('content')
    </main>
</body>
</html>
