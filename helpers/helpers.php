<?php


class helpers{
/*
 * helpers.php
 * Funções utilitárias usadas em todos os endpoints da API.
 *
 * Como usar:
 *   resposta_json(true, 'Mensagem', $dados);
 *   verificar_login();
 *   verificar_admin();
 */


/*
 * Envia uma resposta JSON padronizada e encerra o script.
 *
 * $sucesso  → true ou false
 * $mensagem → texto exibido para o usuário / frontend
 * $dados    → array com os dados retornados (opcional)
 * $status   → código HTTP (200, 201, 401, 404, etc.)
 */
static function resposta_json($sucesso, $mensagem, $dados = null, $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => $sucesso,
        'message' => $mensagem,
        'data'    => $dados
    ], JSON_UNESCAPED_UNICODE);
    exit;
}


/*
 * Verifica se o usuário está logado.
 * Se não estiver, retorna 401 e encerra.
 */
function verificar_login()
{
    if (empty($_SESSION['usuario_id'])) {
        helpers::resposta_json(false, 'Você precisa estar logado para acessar esta funcionalidade.', null, 401);
    }
}


/*
 * Verifica se o usuário é administrador.
 * Se não for (ou não estiver logado), retorna 403 e encerra.
 */
function verificar_admin()
{
    helpers::verificar_login();

    if (empty($_SESSION['admin']) || $_SESSION['admin'] !== true) {
        helpers::resposta_json(false, 'Você não tem permissão para realizar esta ação.', null, 403);
    }
}
}