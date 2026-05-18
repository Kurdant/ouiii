<?php

declare(strict_types=1);

// Partial : fil d'ariane contextuel.
// Usage depuis une vue :
//   $breadcrumb = [
//       ['label' => 'Commandes', 'href' => '/commandes'],
//       ['label' => 'Commande R-123456'],   // dernier élément : pas de href
//   ];
//   include __DIR__ . '/../partials/breadcrumb.php';
//
// Si $breadcrumb est vide ou non défini, le partial ne produit rien.

/** @var list<array{label: string, href?: string}> $breadcrumb */
$breadcrumb = $breadcrumb ?? [];
if ($breadcrumb === []) {
    return;
}

$dernierIndex = count($breadcrumb) - 1;
?>
<nav class="breadcrumb" aria-label="Fil d'ariane">
    <ol>
        <?php foreach ($breadcrumb as $index => $item): ?>
            <?php
            $label = htmlspecialchars((string) ($item['label'] ?? ''), ENT_QUOTES, 'UTF-8');
            $href  = isset($item['href']) ? (string) $item['href'] : null;
            ?>
            <?php if ($index === $dernierIndex || $href === null): ?>
                <li aria-current="page"><?= $label ?></li>
            <?php else: ?>
                <li>
                    <a href="<?= htmlspecialchars($href, ENT_QUOTES, 'UTF-8') ?>"><?= $label ?></a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ol>
</nav>
