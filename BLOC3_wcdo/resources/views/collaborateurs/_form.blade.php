@php $isEdit = $isEdit ?? false; @endphp
<div class="form-group">
    <label for="nom">Nom *</label>
    <input type="text" id="nom" name="nom" value="{{ old('nom', $collaborateur->nom ?? '') }}" maxlength="100" required>
    @error('nom') <div class="error-text">{{ $message }}</div> @enderror
</div>
<div class="form-group">
    <label for="prenom">Prénom *</label>
    <input type="text" id="prenom" name="prenom" value="{{ old('prenom', $collaborateur->prenom ?? '') }}" maxlength="100" required>
    @error('prenom') <div class="error-text">{{ $message }}</div> @enderror
</div>
<div class="form-group">
    <label for="email">Email *</label>
    <input type="email" id="email" name="email" value="{{ old('email', $collaborateur->email ?? '') }}" maxlength="180" required>
    @error('email') <div class="error-text">{{ $message }}</div> @enderror
</div>
<div class="form-group">
    <label for="telephone">Téléphone</label>
    <input type="tel" id="telephone" name="telephone" value="{{ old('telephone', $collaborateur->telephone ?? '') }}" maxlength="20">
    @error('telephone') <div class="error-text">{{ $message }}</div> @enderror
</div>
<div class="form-group">
    <label for="date_premiere_embauche">Date de première embauche *</label>
    <input type="date" id="date_premiere_embauche" name="date_premiere_embauche"
           value="{{ old('date_premiere_embauche', isset($collaborateur) && $collaborateur ? $collaborateur->date_premiere_embauche->format('Y-m-d') : '') }}" required>
    @error('date_premiere_embauche') <div class="error-text">{{ $message }}</div> @enderror
</div>
<div class="form-group">
    <label>
        <input type="checkbox" name="administrateur" value="1"
            {{ old('administrateur', $collaborateur->administrateur ?? false) ? 'checked' : '' }}>
        Administrateur (accès à l'application)
    </label>
    @error('administrateur') <div class="error-text">{{ $message }}</div> @enderror
</div>
<div class="form-group">
    <label for="password">Mot de passe {{ $isEdit ? '(laisser vide pour ne pas changer)' : '(obligatoire si administrateur)' }}</label>
    <input type="password" id="password" name="password" minlength="8" maxlength="255" autocomplete="new-password">
    @error('password') <div class="error-text">{{ $message }}</div> @enderror
</div>
