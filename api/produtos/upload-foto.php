<?php

/**
 * api/produtos/upload-foto.php
 * Recebe um arquivo de imagem via POST e salva em assets/img/produtos/.
 * Retorna JSON: { success, url } ou { success, message }.
 */

include_once(__DIR__ . '/../../api/auth/session.php');

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['foto'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Nenhum arquivo enviado.']);
    exit;
}

$file         = $_FILES['foto'];
$maxSize      = 2 * 1024 * 1024; // 2 MB
$allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Erro durante o upload.']);
    exit;
}

if ($file['size'] > $maxSize) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Arquivo muito grande. Máximo 2 MB.']);
    exit;
}

$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);

if (!in_array($mimeType, $allowedMimes, true)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Tipo de arquivo não permitido. Use JPG, PNG, WebP ou GIF.']);
    exit;
}

$ext       = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$filename  = 'produto_' . bin2hex(random_bytes(8)) . '.' . $ext;
$uploadDir = __DIR__ . '/../../assets/img/produtos/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$dest = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Falha ao salvar o arquivo no servidor.']);
    exit;
}

echo json_encode(['success' => true, 'url' => 'assets/img/produtos/' . $filename]);
