<?php

declare(strict_types=1);

// Partial : navigation latérale. Les liens sont filtrés par rôle conformément
// au CDC :
//   - Administration  : tout
//   - Accueil         : commandes (liste + saisie + livraison) + mon compte
//   - Préparation     : commandes (liste filtrée) + mon compte
//
// Les sections n'apparaissent que si elles contiennent au moins un lien
// pour le rôle courant (pas de titre "Catalogue" orphelin).
//
// Le lien correspondant à la page courante reçoit la classe `active`.

$roleCourant   = $_SESSION['user']['role'] ?? '';
$estAdmin      = $roleCourant === 'Administration';
$estAccueil    = $roleCourant === 'Accueil';

// Chemin courant nettoyé de la query string et du trailing slash (sauf racine).
$cheminCourant = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if ($cheminCourant !== '/' && substr($cheminCourant, -1) === '/') {
    $cheminCourant = rtrim($cheminCourant, '/');
}

/**
 * Retourne ' class="active"' si l'URL passée correspond à la page courante.
 * On considère qu'un lien est actif s'il est égal au chemin OU si le chemin
 * commence par le lien + '/' (pour activer "Liste des commandes" sur
 * `/commandes/42`).
 */
$lienActif = static function (string $href) use ($cheminCourant): string {
    if ($href === $cheminCourant) {
        return ' class="active"';
    }
    if ($href !== '/' && str_starts_with($cheminCourant, $href . '/')) {
        return ' class="active"';
    }
    return '';
};

// Visibilité des sections par rôle (vraie si au moins un lien sera affiché).
$voitCommandes = ($estAdmin || $estAccueil || $roleCourant === 'Preparation');
$voitCatalogue = $estAdmin;
$voitGestion   = $estAdmin;
?>
<aside class="site-sidebar">
    <nav aria-label="Navigation principale">
        <ul>
            <?php if ($voitCommandes): ?>
            <li class="nav-section">Commandes</li>
            <li><a href="/commandes"<?= $lienActif('/commandes') ?>>Liste des commandes</a></li>
            <?php if ($estAdmin || $estAccueil): ?>
            <li><a href="/commandes/creer"<?= $lienActif('/commandes/creer') ?>>Nouvelle commande</a></li>
            <li><a href="/commandes/livraison"<?= $lienActif('/commandes/livraison') ?>>Déclarer une livraison</a></li>
            <?php endif; ?>
            <?php endif; ?>

            <?php if ($voitCatalogue): ?>
            <li class="nav-section">Catalogue</li>
            <li><a href="/produits"<?= $lienActif('/produits') ?>>Produits</a></li>
            <li><a href="/categories"<?= $lienActif('/categories') ?>>Catégories</a></li>
            <li><a href="/menus"<?= $lienActif('/menus') ?>>Menus</a></li>
            <?php endif; ?>

            <?php if ($voitGestion): ?>
            <li class="nav-section">Gestion</li>
            <li><a href="/utilisateurs"<?= $lienActif('/utilisateurs') ?>>Utilisateurs</a></li>
            <?php endif; ?>

            <li class="nav-section">Mon compte</li>
            <li>
                <a href="/mon-compte/mot-de-passe"<?= $lienActif('/mon-compte/mot-de-passe') ?>>
                    Changer mon mot de passe
                </a>
            </li>
        </ul>
    </nav>
</aside>
