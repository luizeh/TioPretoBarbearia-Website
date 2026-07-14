<?php

/**
 * Partial: HTML <head> compartilhado entre todas as views do painel.
 * Requer: $pageTitle (string) — definida antes do include.
 */
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="../../assets/img/favicon.ico" type="image/x-icon" />
    <title><?= htmlspecialchars($pageTitle ?? '') ?> — Tio Preto Barbearia</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Barlow:wght@300;400;500;600&family=Barlow+Condensed:wght@600;700&display=swap"
        rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="../../assets/css/admin/app.css" />
    <link rel="stylesheet" href="../../node_modules/sweetalert2/dist/sweetalert2.min.css" />
</head>

<body>
