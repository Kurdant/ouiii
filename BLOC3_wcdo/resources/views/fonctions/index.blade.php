@extends('layouts.app')
@section('title', 'Fonctions')

@section('content')
<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h2>Fonctions</h2>
        <a href="{{ route('fonctions.create') }}" class="btn">+ Nouvelle fonction</a>
    </div>

    <table>
        <thead>
            <tr><th>Intitulé du poste</th><th style="width:120px">Actions</th></tr>
        </thead>
        <tbody>
            @forelse ($fonctions as $fonction)
                <tr>
                    <td>{{ $fonction->intitule_poste }}</td>
                    <td><a href="{{ route('fonctions.edit', $fonction) }}" class="btn btn-secondary">Modifier</a></td>
                </tr>
            @empty
                <tr><td colspan="2" style="text-align:center;color:#888;padding:2rem">Aucune fonction enregistrée.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:1rem">{{ $fonctions->links() }}</div>
</div>
@endsection
