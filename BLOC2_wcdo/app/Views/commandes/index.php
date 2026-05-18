<?php
// Vue : liste des commandes filtrée selon le rôle connecté
// Contrôleur : CommandeController::index — tous rôles
/** @var string                                    $title */
/** @var array{type: string, message: string}|null $flash */
/** @var list<array<string, mixed>>                $commandes */
/** @var bool                                      $isPreparation */
/** @var string|null                               $statutCourant */
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

$peutPreparer = in_array($role, ['Administration', 'Preparation'], true);
$peutLivrer   = in_array($role, ['Administration', 'Accueil'], true);
?>
<div class="page-header">
    <h2><?= $isPreparation ? 'Commandes à préparer' : 'Commandes' ?></h2>
    <?php if (in_array($role, ['Administration', 'Accueil'], true)): ?>
    <a href="/commandes/creer" class="btn btn-primary">Nouvelle commande</a>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../partials/flash.php'; ?>

<?php if (!$isPreparation): ?>
<div class="card">
    <form method="GET" action="/commandes" class="filter-form">
        <label for="filtre-statut">Filtrer par statut :</label>
        <select id="filtre-statut" name="statut" onchange="this.form.submit()">
            <option value="">Tous les statuts</option>
            <?php foreach ($libellesStatut as $code => $libelle): ?>
            <option value="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>"
                <?= $statutCourant === $code ? 'selected' : '' ?>>
                <?= htmlspecialchars($libelle, ENT_QUOTES, 'UTF-8') ?>
            </option>
            <?php endforeach; ?>
        </select>
        <noscript><button type="submit" class="btn btn-secondary btn-sm">Filtrer</button></noscript>
    </form>
</div>
<?php endif; ?>

<div class="card">
    <?php if (empty($commandes)): ?>
        <p class="empty-state">Aucune commande à afficher.</p>
    <?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>N° retrait</th>
                <th>Date</th>
                <th>Retrait prévu</th>
                <th>Service</th>
                <th>Source</th>
                <th>Total</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($commandes as $commande):
                $statut = (string) $commande['statut'];
            ?>
            <tr>
                <td><strong><?= htmlspecialchars((string) $commande['numero_retrait'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                <td><?= htmlspecialchars((string) $commande['date_commande'], ENT_QUOTES, 'UTF-8') ?></td>
                <td>
                    <?= $commande['date_heure_retrait_prevue'] !== null
                        ? htmlspecialchars((string) $commande['date_heure_retrait_prevue'], ENT_QUOTES, 'UTF-8')
                        : '<span class="text-muted">—</span>' ?>
                </td>
                <td><?= $commande['type_service'] === 'sur_place' ? 'Sur place' : 'À emporter' ?></td>
                <td><?= $commande['source'] === 'api' ? 'API' : 'Back-office' ?></td>
                <td><?= number_format((float) $commande['total'], 2, ',', ' ') ?>&nbsp;€</td>
                <td>
                    <span class="badge <?= $badgesStatut[$statut] ?? 'badge-secondary' ?>">
                        <?= htmlspecialchars($libellesStatut[$statut] ?? $statut, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                </td>
                <td class="actions">
                    <a href="/commandes/<?= (int) $commande['id_commande'] ?>"
                       class="btn btn-secondary btn-sm">Détail</a>

                    <?php if ($peutPreparer && $statut === 'a_preparer'): ?>
                    <form method="POST"
                          action="/commandes/<?= (int) $commande['id_commande'] ?>/preparee"
                          style="display:inline"
                          novalidate>
                        <input type="hidden" name="_csrf"
                               value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="btn btn-primary btn-sm">Préparée</button>
                    </form>
                    <?php endif; ?>

                    <?php if ($peutLivrer && $statut === 'preparee'): ?>
                    <form method="POST"
                          action="/commandes/<?= (int) $commande['id_commande'] ?>/livree"
                          style="display:inline"
                          novalidate>
                        <input type="hidden" name="_csrf"
                               value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="btn btn-success btn-sm">Livrée</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
