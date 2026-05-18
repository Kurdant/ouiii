<?php
// Vue : gestion des sections et options d'un menu — Administration uniquement
// Contrôleur : SectionMenuController::index
//
// Cette page est la plus dense du back-office catalogue. Elle permet de :
//  - voir la liste des sections du menu et leurs options
//  - ajouter une nouvelle section (POST /menus/{id}/sections)
//  - pour chaque section : ajouter une option produit (POST /sections/{id}/options)
//  - retirer une option (POST /options/{id}/desactiver — soft delete)
//
/** @var string $title */
/** @var array{type: string, message: string}|null $flash */
/** @var array<string, mixed> $menu */
/** @var array<int, array<string, mixed>> $sections */
/** @var array<int, array<string, mixed>> $produitsActifs */
/** @var string $csrfToken */

$menuId    = (int) $menu['id_menu'];
$csrfSafe  = htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8');
?>
<div class="page-header">
    <h2>Composition — <?= htmlspecialchars((string) $menu['nom'], ENT_QUOTES, 'UTF-8') ?></h2>
    <a href="/menus" class="btn btn-secondary">Retour à la liste</a>
</div>

<?php include __DIR__ . '/../partials/flash.php'; ?>

<!-- ─────────────────────────────────────────────────────────────────────── -->
<!-- Liste des sections existantes                                            -->
<!-- ─────────────────────────────────────────────────────────────────────── -->
<?php if (empty($sections)): ?>
    <div class="card">
        <p class="empty-state">
            Aucune section pour ce menu. Ajoutez-en une ci-dessous (ex : « Votre plat »,
            « Votre boisson », « Votre accompagnement »).
        </p>
    </div>
<?php else: ?>
    <?php foreach ($sections as $section): ?>
        <?php $sectionId = (int) $section['id_section_menu']; ?>
        <div class="card section-card">
            <div class="section-header">
                <h3>
                    <?= htmlspecialchars((string) $section['nom'], ENT_QUOTES, 'UTF-8') ?>
                    <?php if ($section['obligatoire']): ?>
                        <span class="badge badge-warning">Obligatoire</span>
                    <?php else: ?>
                        <span class="badge badge-info">Facultative</span>
                    <?php endif; ?>
                </h3>
                <small>
                    Quantité : de <?= (int) $section['quantite_min'] ?>
                    à <?= (int) $section['quantite_max'] ?>
                </small>
            </div>

            <!-- Options de cette section -->
            <?php if (empty($section['options'])): ?>
                <p class="empty-state">Aucune option dans cette section.</p>
            <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Prix produit</th>
                        <th>Supplément</th>
                        <th>Disponible</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($section['options'] as $option): ?>
                    <tr>
                        <td><?= htmlspecialchars((string) $option['produit_nom'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= number_format((float) $option['produit_prix'], 2, ',', ' ') ?>&nbsp;€</td>
                        <td>
                            <?php if ((float) $option['supplement_prix'] > 0): ?>
                                +<?= number_format((float) $option['supplement_prix'], 2, ',', ' ') ?>&nbsp;€
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($option['disponible']): ?>
                                <span class="badge badge-success">Oui</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Non</span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <form method="POST"
                                  action="/options/<?= (int) $option['id_option_menu'] ?>/desactiver"
                                  onsubmit="return confirm(<?= htmlspecialchars(
                                      json_encode('Retirer « ' . $option['produit_nom'] . ' » de cette section ?', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT),
                                      ENT_QUOTES,
                                      'UTF-8'
                                  ) ?>)">
                                <input type="hidden" name="_csrf" value="<?= $csrfSafe ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Retirer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>

            <!-- Formulaire d'ajout d'option dans cette section -->
            <form method="POST"
                  action="/sections/<?= $sectionId ?>/options"
                  class="form form-inline" novalidate>
                <input type="hidden" name="_csrf" value="<?= $csrfSafe ?>">

                <div class="form-group">
                    <label for="id_produit_<?= $sectionId ?>">Ajouter un produit</label>
                    <select id="id_produit_<?= $sectionId ?>" name="id_produit" required>
                        <option value="">— Choisir un produit —</option>
                        <?php foreach ($produitsActifs as $produit): ?>
                        <option value="<?= (int) $produit['id_produit'] ?>">
                            <?= htmlspecialchars((string) $produit['nom'], ENT_QUOTES, 'UTF-8') ?>
                            (<?= number_format((float) $produit['prix'], 2, ',', ' ') ?>&nbsp;€)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="supplement_<?= $sectionId ?>">Supplément (€)</label>
                    <input type="number" id="supplement_<?= $sectionId ?>"
                           name="supplement_prix" step="0.01" min="0" value="0" required>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">Ajouter l'option</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- ─────────────────────────────────────────────────────────────────────── -->
<!-- Formulaire d'ajout d'une nouvelle section                                -->
<!-- ─────────────────────────────────────────────────────────────────────── -->
<div class="card">
    <h3>Ajouter une section</h3>

    <form method="POST" action="/menus/<?= $menuId ?>/sections" class="form" novalidate>
        <input type="hidden" name="_csrf" value="<?= $csrfSafe ?>">

        <div class="form-group">
            <label for="section_nom">Nom de la section <span class="required">*</span></label>
            <input type="text" id="section_nom" name="nom" maxlength="100" required
                   placeholder="ex : Votre plat, Votre boisson…">
        </div>

        <div class="form-group form-checkbox">
            <label>
                <input type="checkbox" name="obligatoire" checked>
                Section obligatoire (le client doit faire un choix)
            </label>
        </div>

        <div class="form-group form-inline-fields">
            <label for="quantite_min">Quantité min <span class="required">*</span></label>
            <input type="number" id="quantite_min" name="quantite_min"
                   min="0" max="99" value="1" required>

            <label for="quantite_max">Quantité max <span class="required">*</span></label>
            <input type="number" id="quantite_max" name="quantite_max"
                   min="1" max="99" value="1" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Ajouter la section</button>
        </div>
    </form>
</div>
