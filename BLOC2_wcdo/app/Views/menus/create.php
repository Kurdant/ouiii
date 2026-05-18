<?php
// Vue : formulaire de création d'un menu — Administration uniquement
// Contrôleur : MenuController::create
/** @var string $title */
/** @var array{type: string, message: string}|null $flash */
/** @var string $csrfToken */
?>
<div class="page-header">
    <h2>Créer un menu</h2>
    <a href="/menus" class="btn btn-secondary">Retour</a>
</div>

<?php include __DIR__ . '/../partials/flash.php'; ?>

<div class="card">
    <form method="POST" action="/menus" enctype="multipart/form-data" class="form" novalidate>
        <input type="hidden" name="_csrf"
               value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

        <div class="form-group">
            <label for="nom">Nom <span class="required">*</span></label>
            <input type="text" id="nom" name="nom" maxlength="150" required>
        </div>

        <div class="form-group">
            <label for="description">Description <span class="required">*</span></label>
            <textarea id="description" name="description" rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label for="prix">Prix (€) <span class="required">*</span></label>
            <input type="number" id="prix" name="prix" step="0.01" min="0.01" required>
        </div>

        <div class="form-group">
            <label for="image">Image <span class="required">*</span></label>
            <input type="file" id="image" name="image"
                   accept="image/jpeg,image/png" required>
            <small>Formats acceptés : JPEG, PNG.</small>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Créer le menu</button>
            <a href="/menus" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
