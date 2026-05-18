@php
    /** @var array $prefill */
    /** @var \Illuminate\Database\Eloquent\Collection $collaborateurs */
    /** @var \Illuminate\Database\Eloquent\Collection $restaurants */
    /** @var \Illuminate\Database\Eloquent\Collection $fonctions */
    $current = $affectation ?? null;
@endphp

<div class="form-group">
    <label for="collaborateur_id">Collaborateur *</label>
    <select id="collaborateur_id" name="collaborateur_id" required>
        <option value="">— Sélectionner —</option>
        @foreach ($collaborateurs as $c)
            <option value="{{ $c->id }}"
                @selected(old('collaborateur_id', $current?->collaborateur_id ?? ($prefill['collaborateur_id'] ?? null)) == $c->id)>
                {{ $c->nom }} {{ $c->prenom }} ({{ $c->email }})
            </option>
        @endforeach
    </select>
    @error('collaborateur_id') <div class="error-text">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="restaurant_id">Restaurant *</label>
    <select id="restaurant_id" name="restaurant_id" required>
        <option value="">— Sélectionner —</option>
        @foreach ($restaurants as $r)
            <option value="{{ $r->id }}"
                @selected(old('restaurant_id', $current?->restaurant_id ?? ($prefill['restaurant_id'] ?? null)) == $r->id)>
                {{ $r->nom }} ({{ $r->ville }})
            </option>
        @endforeach
    </select>
    @error('restaurant_id') <div class="error-text">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="fonction_id">Fonction *</label>
    <select id="fonction_id" name="fonction_id" required>
        <option value="">— Sélectionner —</option>
        @foreach ($fonctions as $f)
            <option value="{{ $f->id }}"
                @selected(old('fonction_id', $current?->fonction_id) == $f->id)>
                {{ $f->intitule_poste }}
            </option>
        @endforeach
    </select>
    @error('fonction_id') <div class="error-text">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="date_debut">Date de début *</label>
    <input type="date" id="date_debut" name="date_debut"
           value="{{ old('date_debut', $current?->date_debut?->format('Y-m-d')) }}" required>
    @error('date_debut') <div class="error-text">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="date_fin">Date de fin <span style="color:#888;font-weight:normal">(vide = affectation non bornée)</span></label>
    <input type="date" id="date_fin" name="date_fin"
           value="{{ old('date_fin', $current?->date_fin?->format('Y-m-d')) }}">
    @error('date_fin') <div class="error-text">{{ $message }}</div> @enderror
</div>
