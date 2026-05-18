<?php
// Vue : liste des menus — Administration uniquement
// Contrôleur : MenuController::index
/** @var string $title */
/** @var array{type: string, message: string}|null $flash */
/** @var array<int, array<string, mixed>> $menus */
/** @var string $csrfToken */
?>
<div class="page-header">
    <h2>Menus</h2>
    <a href="/menus/creer" class="btn btn-primary">Créer un menu</a>
</div>

<?php include __DIR__ . '/../partials/flash.php'; ?>

<div class="card">
    <?php if (empty($menus)): ?>
        <p class="empty-state">Aucun menu trouvé.</p>
    <?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prix</th>
                <th>Disponible</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($menus as $menu): ?>
            <tr class="<?= $menu['actif'] ? '' : 'row-inactive' ?>">
                <td><?= htmlspecialchars((string) $menu['nom'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= number_format((float) $menu['prix'], 2, ',', ' ') ?>&nbsp;€</td>
                <td>
                    <?php if ($menu['disponible']): ?>
                        <span class="badge badge-success">Oui</span>
                    <?php else: ?>
                        <span class="badge badge-warning">Non</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($menu['actif']): ?>
                        <span class="badge badge-success">Actif</span>
                    <?php else: ?>
                        <span class="badge badge-inactive">Inactif</span>
                    <?php endif; ?>
                </td>
                <td class="actions">
                    <a href="/menus/<?= (int) $menu['id_menu'] ?>/sections"
                       class="btn btn-secondary btn-sm">Composition</a>
                    <a href="/menus/<?= (int) $menu['id_menu'] ?>/editer"
                       class="btn btn-secondary btn-sm">Modifier</a>

                    <?php if ($menu['actif']): ?>
                    <form method="POST"
                          action="/menus/<?= (int) $menu['id_menu'] ?>/desactiver"
                          onsubmit="return confirm(<?= htmlspecialchars(
                              json_encode('Désactiver le menu « ' . $menu['nom'] . ' » ?', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT),
                              ENT_QUOTES,
                              'UTF-8'
                          ) ?>)">
                        <input type="hidden" name="_csrf"
                               value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Désactiver</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
