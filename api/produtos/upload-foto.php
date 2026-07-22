<?php

/**
 * api/produtos/upload-foto.php
 * Recebe um arquivo de imagem via POST (multipart) e salva em assets/img/produtos/.
 * Restrito a administradores. Retorna JSON: { success, url } ou { success, message }.
 */

require_once __DIR__ . '/../../helpers/helpers.php';

helpers::iniciarSessao();
helpers::verificar_admin();   // exige login + admin (responde 401/403 em JSON)
helpers::verificarCsrf();     // token via header X-CSRF-Token

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['foto'])) {
    helpers::resposta_json(false, 'Nenhum arquivo enviado.', null, 400);
}

$file    = $_FILES['foto'];
$maxSize = 8 * 1024 * 1024; // 8 MB (fotos de celular)

// Mapa MIME real → extensão. A extensão é derivada do conteúdo validado,
// NUNCA do nome enviado pelo cliente (evita salvar .php disfarçado de imagem).
$mimeParaExt = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
    'image/gif'  => 'gif',
];

if ($file['error'] !== UPLOAD_ERR_OK) {
    helpers::resposta_json(false, 'Erro durante o upload.', null, 400);
}

if ($file['size'] > $maxSize) {
    helpers::resposta_json(false, 'Arquivo muito grande. Máximo 8 MB.', null, 400);
}

$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);

if (!isset($mimeParaExt[$mimeType])) {
    helpers::resposta_json(false, 'Tipo de arquivo não permitido. Use JPG, PNG, WebP ou GIF.', null, 400);
}

$ext       = $mimeParaExt[$mimeType];
$filename  = 'produto_' . bin2hex(random_bytes(8)) . '.' . $ext;
$uploadDir = __DIR__ . '/../../assets/img/produtos/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$dest = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    helpers::resposta_json(false, 'Falha ao salvar o arquivo no servidor.', null, 500);
}

helpers::resposta_json(true, 'Imagem enviada com sucesso.', ['url' => 'assets/img/produtos/' . $filename], 200);
