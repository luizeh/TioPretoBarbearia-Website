<?php

/**
 * api/admin/tags.php
 * CRUD de tags de produtos — área admin.
 */

require_once __DIR__ . '/session_admin.php';
require_once __DIR__ . '/../../config/Connection.php';
require_once __DIR__ . '/../../sql/ProdutosSql.php';
require_once __DIR__ . '/../../helpers/helpers.php';

$pdo    = Connection::getConnection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $tags = ProdutosSql::listarTags($pdo);
    helpers::resposta_json(true, 'OK', $tags, 200);
}

if ($method === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    helpers::verificarCsrf($body);
    $action = $body['action'] ?? '';

    if ($action === 'criar') {
        if (empty($body['nome'])) {
            helpers::resposta_json(false, 'O nome da tag é obrigatório.', null, 400);
        }
        $nome = trim($body['nome']);
        $stmt = $pdo->prepare("INSERT INTO tags (nome) VALUES (:nome)");
        try {
            $stmt->execute([':nome' => $nome]);
            $id = (int) $pdo->lastInsertId();
            helpers::resposta_json(true, 'Tag criada com sucesso.', ['id' => $id, 'nome' => $nome], 201);
        } catch (PDOException $e) {
            helpers::resposta_json(false, 'Já existe uma tag com este nome.', null, 409);
        }
    }

    if ($action === 'editar') {
        if (empty($body['id']) || empty($body['nome'])) {
            helpers::resposta_json(false, 'ID e nome são obrigatórios.', null, 400);
        }
        $stmt = $pdo->prepare("UPDATE tags SET nome = :nome WHERE id = :id");
        $stmt->execute([':nome' => trim($body['nome']), ':id' => (int) $body['id']]);
        helpers::resposta_json(true, 'Tag atualizada com sucesso.', null, 200);
    }

    if ($action === 'excluir') {
        if (empty($body['id'])) {
            helpers::resposta_json(false, 'ID da tag é obrigatório.', null, 400);
        }
        $stmt = $pdo->prepare("DELETE FROM tags WHERE id = :id");
        $stmt->execute([':id' => (int) $body['id']]);
        helpers::resposta_json(true, 'Tag excluída com sucesso.', null, 200);
    }
}

helpers::resposta_json(false, 'Método ou ação não reconhecidos.', null, 400);
