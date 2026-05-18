<?php
/** @var string $title */
/** @var string $content */
/** @var string[] $extraCss   CSS supplémentaires injectés par la vue courante */
$title    = $title    ?? 'Wacdo';
$extraCss = $extraCss ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> — Wacdo</title>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/layout.css">
    <link rel="stylesheet" href="/css/components.css">
    <?php foreach ($extraCss as $css): ?>
    <link rel="stylesheet" href="<?= htmlspecialchars($css, ENT_QUOTES, 'UTF-8') ?>">
    <?php endforeach; ?>
</head>
<body>
<div class="site-wrapper">
    <?php include __DIR__ . '/partials/header.php'; ?>

    <?php include __DIR__ . '/partials/sidebar.php'; ?>

    <main class="site-main">
        <?= $content ?>
    </main>

    <footer class="site-footer">
        <small>Wacdo — Bloc 2</small>
    </footer>
</div>
<script src="/js/app.js"></script>
</body>
</html>
