<?php

/**
 * api/admin/site-config.php
 * Leitura e gravação do conteúdo editável do site — área admin.
 *
 * GET  → retorna todos os itens agrupados por grupo
 * POST → salva o valor de uma chave
 */

require_once __DIR__ . '/session_admin.php';
require_once __DIR__ . '/../../helpers/helpers.php';
require_once __DIR__ . '/../../sql/SiteConfigSql.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    helpers::resposta_json(true, 'OK', SiteConfigSql::buscarTodos());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $body  = json_decode(file_get_contents('php://input'), true) ?? [];
    helpers::verificarCsrf($body);
    $chave = trim((string) ($body['chave'] ?? ''));
    $valor = (string) ($body['valor'] ?? '');

    if ($chave === '') {
        helpers::resposta_json(false, 'Chave é obrigatória.', null, 400);
    }

    try {
        SiteConfigSql::salvar($chave, $valor);
        helpers::resposta_json(true, 'Conteúdo salvo com sucesso.');
    } catch (RuntimeException $e) {
        helpers::resposta_json(false, $e->getMessage(), null, 404);
    }
}

helpers::resposta_json(false, 'Método não permitido.', null, 405);
