<?php

function buscarUsuario($pdo, $id)
{
    $stmt = $pdo->prepare("
        SELECT
            id,
            nome,
            sobrenome,
            email,
            telefone,
            cidade
        FROM usuarios
        WHERE id = :id
    ");

    $stmt->execute([
        ':id' => $id
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


function contarUsuarios($pdo)
{
    return $pdo
        ->query("
            SELECT COUNT(*)
            FROM usuarios
        ")
        ->fetchColumn();
}



function listarUsuarios($pdo, $limite, $offset)
{
    $stmt = $pdo->prepare("
        SELECT
            id,
            nome,
            sobrenome,
            email,
            telefone,
            cidade
        FROM usuarios
        ORDER BY nome
        LIMIT :limite OFFSET :offset
    ");

    $stmt->bindValue(':limite', (int) $limite, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
