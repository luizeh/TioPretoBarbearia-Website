<?php

include_once __DIR__ . '/../config/connection.php';

class UsuariosSql
{
    public static function cadastrar(array $dados): array
    {
        $pdo = Connection::getConnection();

        $checkEmail = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $checkEmail->execute([':email' => $dados['email']]);

        if ($checkEmail->fetch()) {
            return ['success' => false, 'message' => 'E-mail já cadastrado.'];
        }

        $checkTelefone = $pdo->prepare("SELECT id FROM usuarios WHERE telefone = :telefone");
        $checkTelefone->execute([':telefone' => $dados['telefone']]);

        if ($checkTelefone->fetch()) {
            return ['success' => false, 'message' => 'Telefone já cadastrado.'];
        }

        $stmt = $pdo->prepare("
            INSERT INTO usuarios (nome, sobrenome, telefone, email, senha, cidade)
            VALUES (:nome, :sobrenome, :telefone, :email, :senha, :cidade)
        ");

        $stmt->execute([
            ':nome'      => $dados['nome'],
            ':sobrenome' => $dados['sobrenome'],
            ':telefone'  => $dados['telefone'],
            ':email'     => $dados['email'],
            ':senha'     => password_hash($dados['senha'], PASSWORD_DEFAULT),
            ':cidade'    => $dados['cidade'],
        ]);

        return [
            'success' => true,
            'message' => 'Usuário cadastrado com sucesso.',
            'id' => (int) $pdo->lastInsertId(),
        ];
    }

    public static function buscarPorId(int $id): array|false
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT id, nome, sobrenome, email, telefone, cidade FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function atualizar(int $id, array $dados): void
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            UPDATE usuarios
            SET nome = :nome, sobrenome = :sobrenome, email = :email, telefone = :telefone, cidade = :cidade
            WHERE id = :id
        ");
        $stmt->execute([
            ':nome'      => $dados['nome'],
            ':sobrenome' => $dados['sobrenome'],
            ':email'     => $dados['email'],
            ':telefone'  => $dados['telefone'],
            ':cidade'    => $dados['cidade'],
            ':id'        => $id,
        ]);
    }

    public static function emailEmUso(string $email, int $ignorarId): bool
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email AND id <> :id LIMIT 1');
        $stmt->execute([':email' => $email, ':id' => $ignorarId]);
        return (bool) $stmt->fetchColumn();
    }

    public static function excluirConta(int $id): bool
    {
        $pdo = Connection::getConnection();

        try {
            $pdo->beginTransaction();
            $pdo->prepare('DELETE FROM notificacoes WHERE usuario_id = :id')->execute([':id' => $id]);
            $pdo->prepare('DELETE FROM carrinho_itens WHERE carrinho_id IN (SELECT id FROM carrinho WHERE usuario_id = :id)')->execute([':id' => $id]);
            $pdo->prepare('DELETE FROM carrinho WHERE usuario_id = :id')->execute([':id' => $id]);
            $pdo->prepare('DELETE FROM pedido_itens WHERE pedido_id IN (SELECT id FROM pedidos WHERE usuario_id = :id)')->execute([':id' => $id]);
            $pdo->prepare('DELETE FROM pedidos WHERE usuario_id = :id')->execute([':id' => $id]);
            $pdo->prepare('DELETE FROM agendamento_servicos WHERE agendamento_id IN (SELECT id FROM agendamentos WHERE usuario_id = :id)')->execute([':id' => $id]);
            $pdo->prepare('DELETE FROM agendamentos WHERE usuario_id = :id')->execute([':id' => $id]);
            $pdo->prepare('DELETE FROM logs WHERE usuario_id = :id')->execute([':id' => $id]);
            $stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = :id AND admin = 0');
            $stmt->execute([':id' => $id]);
            $pdo->commit();
            return $stmt->rowCount() > 0;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    public static function alterarSenha(int $id, string $senhaAtual, string $novaSenha): array
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row || !password_verify($senhaAtual, $row['senha'])) {
            return ['success' => false, 'message' => 'Senha atual incorreta.'];
        }

        $stmt = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
        $stmt->execute([':senha' => password_hash($novaSenha, PASSWORD_DEFAULT), ':id' => $id]);

        return ['success' => true, 'message' => 'Senha alterada com sucesso.'];
    }
}
