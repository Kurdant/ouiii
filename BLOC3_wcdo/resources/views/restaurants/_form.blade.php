<div class="form-group">
    <label for="nom">Nom *</label>
    <input type="text" id="nom" name="nom" value="{{ old('nom', $restaurant->nom ?? '') }}" maxlength="150" required>
    @error('nom') <div class="error-text">{{ $message }}</div> @enderror
</div>
<div class="form-group">
    <label for="adresse">Adresse *</label>
    <input type="text" id="adresse" name="adresse" value="{{ old('adresse', $restaurant->adresse ?? '') }}" maxlength="255" required>
    @error('adresse') <div class="error-text">{{ $message }}</div> @enderror
</div>
<div class="form-group">
    <label for="code_postal">Code postal *</label>
    <input type="text" id="code_postal" name="code_postal" value="{{ old('code_postal', $restaurant->code_postal ?? '') }}" maxlength="10" required>
    @error('code_postal') <div class="error-text">{{ $message }}</div> @enderror
</div>
<div class="form-group">
    <label for="ville">Ville *</label>
    <input type="text" id="ville" name="ville" value="{{ old('ville', $restaurant->ville ?? '') }}" maxlength="100" required>
    @error('ville') <div class="error-text">{{ $message }}</div> @enderror
</div>
