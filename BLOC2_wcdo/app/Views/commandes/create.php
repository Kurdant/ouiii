<?php
// Vue : formulaire de saisie d'une commande — refonte UX Sprint 12
// Contrôleur : CommandeController::create — Accueil + Administration
/** @var string                                    $title */
/** @var array{type: string, message: string}|null $flash */
/** @var list<array<string, mixed>>                $produits */
/** @var list<array<string, mixed>>                $menus */
/** @var string                                    $csrfToken */
?>
<div class="page-header">
    <h2>Nouvelle commande</h2>
    <a href="/commandes" class="btn btn-secondary">Annuler</a>
</div>

<?php include __DIR__ . '/../partials/flash.php'; ?>

<form method="POST" action="/commandes" novalidate class="order-form">
    <input type="hidden" name="_csrf"
           value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">

    <!-- 1. Type de service ----------------------------------------------- -->
    <section class="service-toggle">
        <label class="service-card">
            <input type="radio" name="type_service" value="sur_place" checked>
            <span class="service-label">Sur place</span>
        </label>
        <label class="service-card">
            <input type="radio" name="type_service" value="a_emporter">
            <span class="service-label">À emporter</span>
        </label>
    </section>

    <div class="order-layout">
        <main class="order-main">

            <!-- 2. Onglets Produits / Menus ----------------------------- -->
            <nav class="tabs" role="tablist">
                <button type="button" class="tab active" data-tab="produits">Produits</button>
                <button type="button" class="tab" data-tab="menus">Menus</button>
            </nav>

            <!-- 3. Grille produits -------------------------------------- -->
            <section id="tab-produits" class="product-grid">
                <?php if (empty($produits)): ?>
                    <p class="empty-state">Aucun produit disponible actuellement.</p>
                <?php else: foreach ($produits as $produit):
                    $idProduit = (int) $produit['id_produit'];
                    $prix      = (float) $produit['prix'];
                ?>
                <article class="product-card" data-prix="<?= htmlspecialchars(number_format($prix, 2, '.', ''), ENT_QUOTES, 'UTF-8') ?>">
                    <h3 class="product-name"><?= htmlspecialchars((string) $produit['nom'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <span class="product-price"><?= number_format($prix, 2, ',', '&nbsp;') ?>&nbsp;€</span>
                    <div class="counter">
                        <button type="button" class="counter-btn minus" aria-label="Retirer">&minus;</button>
                        <span class="counter-value">0</span>
                        <button type="button" class="counter-btn plus" aria-label="Ajouter">+</button>
                    </div>
                    <input type="hidden" name="produits[<?= $idProduit ?>]" value="0">
                </article>
                <?php endforeach; endif; ?>
            </section>

            <!-- 4. Liste menus dépliables ------------------------------- -->
            <section id="tab-menus" class="menu-list hidden">
                <?php if (empty($menus)): ?>
                    <p class="empty-state">Aucun menu disponible actuellement.</p>
                <?php else: foreach ($menus as $menu):
                    $idMenu   = (int) $menu['id_menu'];
                    $prixMenu = (float) $menu['prix'];
                ?>
                <article class="menu-block" data-id="<?= $idMenu ?>" data-prix="<?= htmlspecialchars(number_format($prixMenu, 2, '.', ''), ENT_QUOTES, 'UTF-8') ?>">
                    <header class="menu-header">
                        <div class="menu-title">
                            <h3><?= htmlspecialchars((string) $menu['nom'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <span class="menu-price"><?= number_format($prixMenu, 2, ',', '&nbsp;') ?>&nbsp;€</span>
                        </div>
                        <span class="menu-toggle" aria-hidden="true">&#9660;</span>
                    </header>

                    <?php if (!empty($menu['sections'])): ?>
                    <div class="menu-sections" hidden>
                        <?php foreach ($menu['sections'] as $section):
                            $idSection   = (int) $section['id_section_menu'];
                            $obligatoire = (bool) $section['obligatoire'];
                            $qMin        = (int) $section['quantite_min'];
                            $qMax        = (int) $section['quantite_max'];
                            $multiple    = $qMax > 1;
                            $inputType   = $multiple ? 'checkbox' : 'radio';
                            $nameSuffix  = $multiple ? '[]' : '';
                            $nameAttr    = "menus[{$idMenu}][sections][{$idSection}]{$nameSuffix}";
                            $hint = $qMin === $qMax
                                ? ($qMin === 1 ? '1 choix' : $qMin . ' choix')
                                : 'entre ' . $qMin . ' et ' . $qMax . ' choix';
                        ?>
                        <fieldset class="section-block" data-section-id="<?= $idSection ?>" data-min="<?= $qMin ?>" data-max="<?= $qMax ?>">
                            <legend>
                                <span class="section-name"><?= htmlspecialchars((string) $section['nom'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php if ($obligatoire): ?>
                                    <span class="badge badge-required">Obligatoire</span>
                                <?php else: ?>
                                    <span class="badge badge-optional">Facultatif</span>
                                <?php endif; ?>
                                <span class="section-hint"><?= htmlspecialchars($hint, ENT_QUOTES, 'UTF-8') ?></span>
                            </legend>
                            <?php if (empty($section['options'])): ?>
                                <p class="empty-state">Aucune option configurée.</p>
                            <?php else: foreach ($section['options'] as $option):
                                $idOption  = (int) $option['id_produit'];
                                $supp      = (float) $option['supplement_prix'];
                            ?>
                            <label class="option-chip">
                                <input type="<?= $inputType ?>"
                                       name="<?= htmlspecialchars($nameAttr, ENT_QUOTES, 'UTF-8') ?>"
                                       value="<?= $idOption ?>"
                                       data-supplement="<?= htmlspecialchars(number_format($supp, 2, '.', ''), ENT_QUOTES, 'UTF-8') ?>"
                                       data-label="<?= htmlspecialchars((string) $option['produit_nom'], ENT_QUOTES, 'UTF-8') ?>">  
                                <span class="option-label">
                                    <?= htmlspecialchars((string) $option['produit_nom'], ENT_QUOTES, 'UTF-8') ?>
                                    <?php if ($supp > 0): ?>
                                        <span class="option-supp">+<?= number_format($supp, 2, ',', '&nbsp;') ?>&nbsp;€</span>
                                    <?php endif; ?>
                                </span>
                            </label>
                            <?php endforeach; endif; ?>
                        </fieldset>
                        <?php endforeach; ?>
                        <div class="menu-add-row">
                            <button type="button" class="btn-add-menu">Ajouter ce menu</button>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="menu-instances"></div>
                </article>
                <?php endforeach; endif; ?>
            </section>
        </main>

        <!-- 5. Panier sticky ---------------------------------------------- -->
        <aside class="cart-summary">
            <h2 class="cart-title">Panier</h2>
            <ul class="cart-items">
                <li class="cart-empty">Aucun article sélectionné.</li>
            </ul>
            <div class="cart-total">
                <span>Total</span>
                <strong class="cart-total-value">0,00&nbsp;€</strong>
            </div>
            <p class="cart-count"><span class="cart-count-value">0</span> ligne(s)</p>
            <p class="cart-error hidden">Vérifiez les sections obligatoires des menus.</p>
            <button type="submit" class="btn btn-primary btn-cta" disabled>Créer la commande</button>
            <a href="/commandes" class="btn btn-secondary btn-block">Annuler</a>
        </aside>
    </div>
</form>

<script src="/js/commande-create.js" defer></script>
