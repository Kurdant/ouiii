<?php
// Vue : tableau de bord Administration.
// Affiche 4 compteurs métier + raccourcis vers les actions clés.
// Contrôleur : DashboardController::index

/** @var array{a_preparer: int, preparees: int, livrees_jour: int, total_jour: int} $compteurs */
/** @var array{type: string, message: string}|null $flash */
?>
<?php include __DIR__ . '/../partials/flash.php'; ?>

<header class="page-header">
    <h2>Tableau de bord</h2>
    <p class="page-subtitle">
        Bonjour
        <strong><?= htmlspecialchars($_SESSION['user']['identifiant'] ?? '', ENT_QUOTES, 'UTF-8') ?></strong>,
        voici l'activité du restaurant aujourd'hui.
    </p>
</header>

<section class="dashboard-stats">
    <article class="stat-card">
        <span class="stat-label">À préparer</span>
        <span class="stat-value"><?= (int) $compteurs['a_preparer'] ?></span>
    </article>
    <article class="stat-card">
        <span class="stat-label">Préparées (prêtes à livrer)</span>
        <span class="stat-value"><?= (int) $compteurs['preparees'] ?></span>
    </article>
    <article class="stat-card">
        <span class="stat-label">Livrées aujourd'hui</span>
        <span class="stat-value"><?= (int) $compteurs['livrees_jour'] ?></span>
    </article>
    <article class="stat-card">
        <span class="stat-label">Total du jour</span>
        <span class="stat-value"><?= (int) $compteurs['total_jour'] ?></span>
    </article>
</section>

<section class="dashboard-shortcuts">
    <h3>Raccourcis</h3>
    <ul>
        <li><a href="/commandes">Liste des commandes</a></li>
        <li><a href="/commandes/creer">Nouvelle commande</a></li>
        <li><a href="/produits">Gérer les produits</a></li>
        <li><a href="/utilisateurs">Gérer les utilisateurs</a></li>
    </ul>
</section>
