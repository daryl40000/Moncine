<?php
/** @var string $templateFile */
/** @var string $backUrl URL de retour vers la liste */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Moncine\View::escape($pageTitle ?? MONCINE_APP_NAME) ?> — <?= Moncine\View::escape(MONCINE_APP_NAME) ?></title>
    <link rel="icon" href="/assets/img/favicon.ico" sizes="any">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/print.css">
</head>
<body class="print-page">
    <div class="print-toolbar no-print">
        <button type="button" class="btn btn-primary" id="print-trigger">Imprimer / Enregistrer en PDF</button>
        <?php if (!empty($backUrl)): ?>
            <a href="<?= Moncine\View::escape((string) $backUrl) ?>" class="btn btn-secondary">← Retour à la liste</a>
        <?php endif; ?>
    </div>
    <main class="print-page__main">
        <?php require $templateFile; ?>
    </main>
    <footer class="print-page__footer">
        <p><?= Moncine\View::escape(MONCINE_APP_NAME) ?> — généré le <?= Moncine\View::escape(date('d/m/Y à H:i')) ?></p>
    </footer>
    <script src="/assets/js/print-page.js" defer></script>
</body>
</html>
