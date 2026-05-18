<div class="form-group">
    <label for="intitule_poste">Intitulé du poste *</label>
    <input type="text" id="intitule_poste" name="intitule_poste"
           value="{{ old('intitule_poste', $fonction->intitule_poste ?? '') }}"
           maxlength="120" required autofocus>
    @error('intitule_poste') <div class="error-text">{{ $message }}</div> @enderror
</div>
