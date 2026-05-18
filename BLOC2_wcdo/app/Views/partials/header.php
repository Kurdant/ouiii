<?php

declare(strict_types=1);

// Partial : barre supérieure (logo, identifiant connecté, badge rôle, bouton déconnexion).
// Inclus depuis layout.php.
?>
<header class="site-header">
    <h1>Wacdo</h1>
    <?php if (!empty($_SESSION['user'])): ?>
    <div class="header-user">
        <span class="header-user-name">
            <?= htmlspecialchars($_SESSION['user']['identifiant'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        </span>
        <span class="header-user-role badge badge-info">
            <?= htmlspecialchars($_SESSION['user']['role'] ?? '', ENT_QUOTES, 'UTF-8') ?>
        </span>
        <form method="POST" action="/logout" style="display:inline" novalidate>
            <input type="hidden" name="_csrf"
                   value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="btn btn-secondary btn-sm">Déconnexion</button>
        </form>
    </div>
    <?php endif; ?>
</header>
