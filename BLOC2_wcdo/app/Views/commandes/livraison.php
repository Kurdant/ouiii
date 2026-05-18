<?php
// Vue : formulaire de saisie du numéro de retrait pour livraison au comptoir
// Contrôleur : CommandeController::livraisonForm — Accueil + Administration
/** @var string                                    $title */
/** @var array{type: string, message: string}|null $flash */
/** @var string                                    $csrfToken */
?>
<div class="page-header">
    <h2>Déclarer une livraison</h2>
    <a href="/commandes" class="btn btn-secondary">Retour à la liste</a>
</div>

<?php include __DIR__ . '/../partials/flash.php'; ?>

<div class="card">
    <p class="text-muted">
        Saisissez le numéro de retrait inscrit sur le ticket du client pour
        déclarer la commande livrée. La commande doit être au statut « Préparée ».
    </p>

    <form method="POST" action="/commandes/livraison" novalidate>
        <input type="hidden" name="_csrf"
               value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <div class="form-group">
            <label for="numero_retrait">
                Numéro de retrait <span class="required">*</span>
            </label>
            <input type="text"
                   id="numero_retrait"
                   name="numero_retrait"
                   placeholder="R-123456"
                   maxlength="30"
                   autocomplete="off"
                   autofocus
                   required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success">Confirmer la livraison</button>
            <a href="/commandes" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
