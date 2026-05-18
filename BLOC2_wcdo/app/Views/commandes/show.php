<?php
// Vue : détail d'une commande
// Contrôleur : CommandeController::show — tous rôles
/** @var string                                    $title */
/** @var array{type: string, message: string}|null $flash */
/** @var array<string, mixed>                      $commande */
/** @var string|null                               $role */
/** @var string                                    $csrfToken */

$libellesStatut = [
    'a_preparer' => 'À préparer',
    'preparee'   => 'Préparée',
    'livree'     => 'Livrée',
];
$badgesStatut = [
    'a_preparer' => 'badge-warning',
    'preparee'   => 'badge-info',
    'livree'     => 'badge-success',
];

$statut       = (string) $commande['statut'];
$peutPreparer = in_array($role, ['Administration', 'Preparation'], true) && $statut === 'a_preparer';
$peutLivrer   = in_array($role, ['Administration', 'Accueil'], true) && $statut === 'preparee';
?>
<div class="page-header">
    <h2>Commande <?= htmlspecialchars((string) $commande['numero_retrait'], ENT_QUOTES, 'UTF-8') ?></h2>
    <a href="/commandes" class="btn btn-secondary">Retour à la liste</a>
</div>

<?php include __DIR__ . '/../partials/flash.php'; ?>

<div class="card">
    <h3>Récapitulatif</h3>
    <dl class="definition-list">
        <dt>Numéro de retrait</dt>
        <dd><strong><?= htmlspecialchars((string) $commande['numero_retrait'], ENT_QUOTES, 'UTF-8') ?></strong></dd>

        <dt>Statut</dt>
        <dd>
            <span class="badge <?= $badgesStatut[$statut] ?? 'badge-secondary' ?>">
                <?= htmlspecialchars($libellesStatut[$statut] ?? $statut, ENT_QUOTES, 'UTF-8') ?>
            </span>
        </dd>

        <dt>Type de service</dt>
        <dd><?= $commande['type_service'] === 'sur_place' ? 'Sur place' : 'À emporter' ?></dd>

        <dt>Source</dt>
        <dd><?= $commande['source'] === 'api' ? 'API externe' : 'Saisie back-office' ?></dd>

        <dt>Date de commande</dt>
        <dd><?= htmlspecialchars((string) $commande['date_commande'], ENT_QUOTES, 'UTF-8') ?></dd>

        <dt>Retrait prévu</dt>
        <dd>
            <?= $commande['date_heure_retrait_prevue'] !== null
                ? htmlspecialchars((string) $commande['date_heure_retrait_prevue'], ENT_QUOTES, 'UTF-8')
                : '<span class="text-muted">Non renseigné</span>' ?>
        </dd>

        <dt>Date de préparation</dt>
        <dd>
            <?= $commande['date_preparation'] !== null
                ? htmlspecialchars((string) $commande['date_preparation'], ENT_QUOTES, 'UTF-8')
                : '<span class="text-muted">—</span>' ?>
        </dd>

        <dt>Date de livraison</dt>
        <dd>
            <?= $commande['date_livraison'] !== null
                ? htmlspecialchars((string) $commande['date_livraison'], ENT_QUOTES, 'UTF-8')
                : '<span class="text-muted">—</span>' ?>
        </dd>

        <dt>Auteur</dt>
        <dd>
            <?= !empty($commande['auteur_identifiant'])
                ? htmlspecialchars((string) $commande['auteur_identifiant'], ENT_QUOTES, 'UTF-8')
                : '<span class="text-muted">Système (API)</span>' ?>
        </dd>

        <dt>Total</dt>
        <dd><strong><?= number_format((float) $commande['total'], 2, ',', ' ') ?>&nbsp;€</strong></dd>
    </dl>

    <?php if ($peutPreparer || $peutLivrer): ?>
    <div class="form-actions">
        <?php if ($peutPreparer): ?>
        <form method="POST" action="/commandes/<?= (int) $commande['id_commande'] ?>/preparee" novalidate>
            <input type="hidden" name="_csrf"
                   value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="btn btn-primary">Déclarer préparée</button>
        </form>
        <?php endif; ?>
        <?php if ($peutLivrer): ?>
        <form method="POST" action="/commandes/<?= (int) $commande['id_commande'] ?>/livree" novalidate>
            <input type="hidden" name="_csrf"
                   value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="btn btn-success">Déclarer livrée</button>
        </form>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<div class="card">
    <h3>Lignes de la commande</h3>
    <?php if (empty($commande['lignes'])): ?>
        <p class="empty-state">Aucune ligne enregistrée.</p>
    <?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Article</th>
                <th>Type</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Sous-total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($commande['lignes'] as $ligne): ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars((string) $ligne['libelle_article'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <?php if (!empty($ligne['choix'])): ?>
                    <ul class="choix-list">
                        <?php foreach ($ligne['choix'] as $choix): ?>
                        <li>
                            <em><?= htmlspecialchars((string) $choix['nom_section'], ENT_QUOTES, 'UTF-8') ?> :</em>
                            <?= htmlspecialchars((string) $choix['libelle_produit'], ENT_QUOTES, 'UTF-8') ?>
                            <?php if ((float) $choix['prix_supplement_applique'] > 0): ?>
                                (+ <?= number_format((float) $choix['prix_supplement_applique'], 2, ',', ' ') ?>&nbsp;€)
                            <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </td>
                <td><?= $ligne['type_ligne'] === 'menu' ? 'Menu' : 'Produit' ?></td>
                <td><?= (int) $ligne['quantite'] ?></td>
                <td><?= number_format((float) $ligne['prix_unitaire_applique'], 2, ',', ' ') ?>&nbsp;€</td>
                <td><?= number_format((float) $ligne['sous_total'], 2, ',', ' ') ?>&nbsp;€</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
