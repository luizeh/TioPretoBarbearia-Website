<?php

/**
 * api/user/perfil.php
 * GET  → retorna dados do perfil do usuário logado
 * POST action=editar → atualiza nome, sobrenome, telefone, cidade
 * POST action=senha  → altera senha
 */

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Não autenticado.']);
    exit;
}

require_once __DIR__ . '/../../sql/UsuariosSql.php';
require_once __DIR__ . '/../../helpers/helpers.php';

$id     = (int) $_SESSION['usuario_id'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $usuario = UsuariosSql::buscarPorId($id);
    helpers::resposta_json(true, 'OK', $usuario, 200);
}

if ($method === 'POST') {
    $body   = json_decode(file_get_contents('php://input'), true) ?? [];
    $action = $body['action'] ?? '';

    if ($action === 'editar') {
        $required = ['nome', 'sobrenome', 'cidade'];
        foreach ($required as $field) {
            if (empty(trim($body[$field] ?? ''))) {
                helpers::resposta_json(false, "Campo obrigatório: {$field}.", null, 400);
            }
        }
        $dados = [
            'nome'      => trim($body['nome']),
            'sobrenome' => trim($body['sobrenome']),
            'telefone'  => trim($body['telefone'] ?? ''),
            'cidade'    => trim($body['cidade']),
        ];
        UsuariosSql::atualizar($id, $dados);
        // Atualiza o nome na sessão
        $_SESSION['usuario_nome'] = $dados['nome'];
        helpers::resposta_json(true, 'Perfil atualizado com sucesso.', null, 200);
    }

    if ($action === 'senha') {
        $senhaAtual = $body['senha_atual'] ?? '';
        $novaSenha  = $body['nova_senha']  ?? '';
        if (strlen($novaSenha) < 8) {
            helpers::resposta_json(false, 'A nova senha deve ter no mínimo 8 caracteres.', null, 400);
        }
        $result = UsuariosSql::alterarSenha($id, $senhaAtual, $novaSenha);
        $code   = $result['success'] ? 200 : 400;
        helpers::resposta_json($result['success'], $result['message'], null, $code);
    }
}

helpers::resposta_json(false, 'Método ou ação inválidos.', null, 400);
