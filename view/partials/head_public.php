<?php

/**
 * Partial: head_public.php — HTML <head> compartilhado das páginas públicas.
 *
 * Variáveis esperadas antes do include:
 *   $rootPath  — caminho até a raiz do projeto (ex: '../' de view/, '../../' de view/user/)
 *   $pageTitle — título da aba (opcional; default: 'Tio Preto Barbearia')
 *   $extraCss  — array de caminhos CSS adicionais relativos à raiz (opcional)
 *   $bodyClass — classe(s) para o <body> (opcional)
 */
$pageTitle = $pageTitle ?? 'Tio Preto Barbearia';
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="shortcut icon" href="<?= $rootPath ?? '' ?>assets/img/favicon.png" type="image/png" />
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Barlow:wght@300;400;500;600&family=Barlow+Condensed:wght@600;700&display=swap"
        rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="<?= $rootPath ?? '' ?>assets/css/style.css" />
    <link rel="stylesheet" href="<?= $rootPath ?? '' ?>node_modules/sweetalert2/dist/sweetalert2.min.css" />
    <?php foreach ($extraCss ?? [] as $css): ?>
        <link rel="stylesheet" href="<?= $rootPath ?? '' ?><?= htmlspecialchars($css) ?>" />
    <?php endforeach; ?>
</head>

<body<?= isset($bodyClass) ? ' class="' . htmlspecialchars($bodyClass) . '"' : '' ?>>
    <script src="<?= $rootPath ?? '' ?>node_modules/sweetalert2/dist/sweetalert2.all.min.js"></script>
    <script src="<?= $rootPath ?? '' ?>assets/js/swal-theme.js"></script>