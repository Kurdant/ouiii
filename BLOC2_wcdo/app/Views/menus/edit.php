<?php
// Vue : formulaire d'édition d'un menu — Administration uniquement
// Contrôleur : MenuController::edit
/** @var string $title */
/** @var array{type: string, message: string}|null $flash */
/** @var array<string, mixed> $menu */
/** @var string $csrfToken */

$menuId = (int) $menu['id_menu'];
?>
<div class="page-header">
    <h2>Modifier le menu</h2>
    <a href="/menus" class="btn btn-secondary">Retour</a>
</div>

<?php include __DIR__ . '/../partials/flash.php'; ?>

<div class="card">
    <form method="POST" action="/menus/<?= $menuId ?>" enctype="multipart/form-data" class="form" novalidate>
        <input type="hidden" name="_csrf"
               value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

        <div class="form-group">
            <label for="nom">Nom <span class="required">*</span></label>
            <input type="text" id="nom" name="nom" maxlength="150" required
                   value="<?= htmlspecialchars((string) $menu['nom'], ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="form-group">
            <label for="description">Description <span class="required">*</span></label>
            <textarea id="description" name="description" rows="4" required><?= htmlspecialchars((string) $menu['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
        </div>

        <div class="form-group">
            <label for="prix">Prix (€) <span class="required">*</span></label>
            <input type="number" id="prix" name="prix" step="0.01" min="0.01" required
                   value="<?= number_format((float) $menu['prix'], 2, '.', '') ?>">
        </div>

        <div class="form-group form-checkbox">
            <label>
                <input type="checkbox" name="disponible" <?= $menu['disponible'] ? 'checked' : '' ?>>
                Disponible à la commande
            </label>
        </div>

        <div class="form-group">
            <label for="image">Image (optionnel — laisser vide pour conserver l'actuelle)</label>
            <input type="file" id="image" name="image" accept="image/jpeg,image/png">
            <?php if (!empty($menu['image'])): ?>
            <small>Image actuelle : <?= htmlspecialchars((string) $menu['image'], ENT_QUOTES, 'UTF-8') ?></small>
            <?php endif; ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="/menus/<?= $menuId ?>/sections" class="btn btn-secondary">Gérer la composition</a>
            <a href="/menus" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
