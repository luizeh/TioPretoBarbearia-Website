<?php

include_once __DIR__ . '/../config/Connection.php';

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

    /** Existe uma conta com este e-mail? (usado na validação prévia do cadastro) */
    public static function emailExiste(string $email): bool
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return (bool) $stmt->fetchColumn();
    }

    /** Existe uma conta com este telefone? */
    public static function telefoneExiste(string $telefone): bool
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE telefone = :telefone LIMIT 1");
        $stmt->execute([':telefone' => $telefone]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Insere um cliente JÁ verificado (e-mail e telefone confirmados por código).
     * Usado ao concluir o cadastro em duas etapas — a conta só passa a existir
     * no banco neste momento. A senha já vem em hash ($dados['senha_hash']).
     * Rechecar unicidade evita corrida (mesmo e-mail/telefone criado enquanto pendente).
     */
    public static function cadastrarVerificado(array $dados): array
    {
        $pdo = Connection::getConnection();

        if (self::emailExiste($dados['email'])) {
            return ['success' => false, 'message' => 'E-mail já cadastrado.'];
        }
        if (self::telefoneExiste($dados['telefone'])) {
            return ['success' => false, 'message' => 'Telefone já cadastrado.'];
        }

        $stmt = $pdo->prepare("
            INSERT INTO usuarios
                (nome, sobrenome, telefone, email, senha, cidade,
                 email_verificado, email_verificado_em, telefone_verificado, telefone_verificado_em)
            VALUES
                (:nome, :sobrenome, :telefone, :email, :senha, :cidade,
                 1, NOW(), 1, NOW())
        ");
        $stmt->execute([
            ':nome'      => $dados['nome'],
            ':sobrenome' => $dados['sobrenome'],
            ':telefone'  => $dados['telefone'],
            ':email'     => $dados['email'],
            ':senha'     => $dados['senha_hash'],
            ':cidade'    => $dados['cidade'],
        ]);

        return ['success' => true, 'id' => (int) $pdo->lastInsertId()];
    }

    public static function buscarPorId(int $id): array|false
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT id, nome, sobrenome, email, telefone, cidade FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um usuário pelo e-mail (linha completa) — usado na recuperação de senha.
     */
    public static function buscarPorEmail(string $email): array|false
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca um usuário pelo telefone (só dígitos) — usado na recuperação de senha.
     */
    public static function buscarPorTelefone(string $telefone): array|false
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE telefone = :telefone LIMIT 1");
        $stmt->execute([':telefone' => $telefone]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Retorna o estado de verificação (e pendências) da conta.
     */
    public static function statusVerificacao(int $id): array|false
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare(
            "SELECT id, nome, email, telefone,
                    email_verificado, telefone_verificado,
                    email_pendente, telefone_pendente
             FROM usuarios WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Atualiza apenas os dados que não exigem reverificação (nome, sobrenome, cidade).
     * E-mail e telefone têm fluxo próprio (definir pendente → confirmar por código).
     */
    public static function atualizarDadosBasicos(int $id, array $dados): void
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare(
            "UPDATE usuarios SET nome = :nome, sobrenome = :sobrenome, cidade = :cidade WHERE id = :id"
        );
        $stmt->execute([
            ':nome'      => $dados['nome'],
            ':sobrenome' => $dados['sobrenome'],
            ':cidade'    => $dados['cidade'],
            ':id'        => $id,
        ]);
    }

    public static function telefoneEmUso(string $telefone, int $ignorarId): bool
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare('SELECT id FROM usuarios WHERE telefone = :telefone AND id <> :id LIMIT 1');
        $stmt->execute([':telefone' => $telefone, ':id' => $ignorarId]);
        return (bool) $stmt->fetchColumn();
    }

    // ── Verificação de e-mail/telefone ──────────────────────────────

    public static function marcarEmailVerificado(int $id): void
    {
        Connection::getConnection()
            ->prepare('UPDATE usuarios SET email_verificado = 1, email_verificado_em = NOW() WHERE id = :id')
            ->execute([':id' => $id]);
    }

    public static function marcarTelefoneVerificado(int $id): void
    {
        Connection::getConnection()
            ->prepare('UPDATE usuarios SET telefone_verificado = 1, telefone_verificado_em = NOW() WHERE id = :id')
            ->execute([':id' => $id]);
    }

    // ── Troca de e-mail/telefone (pendente até nova verificação) ─────

    /**
     * Guarda um novo e-mail como pendente — o e-mail atual continua válido.
     */
    public static function definirEmailPendente(int $id, string $email): void
    {
        Connection::getConnection()
            ->prepare('UPDATE usuarios SET email_pendente = :email WHERE id = :id')
            ->execute([':email' => $email, ':id' => $id]);
    }

    /**
     * Guarda um novo telefone como pendente — o telefone atual continua válido.
     */
    public static function definirTelefonePendente(int $id, string $telefone): void
    {
        Connection::getConnection()
            ->prepare('UPDATE usuarios SET telefone_pendente = :tel WHERE id = :id')
            ->execute([':tel' => $telefone, ':id' => $id]);
    }

    /**
     * Confirma a troca de e-mail: promove o pendente a definitivo, verifica e
     * atualiza a data. Retorna o novo e-mail, ou null se não havia pendente.
     */
    public static function confirmarEmailPendente(int $id): ?string
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare('SELECT email_pendente FROM usuarios WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $novo = $stmt->fetchColumn();
        if (empty($novo)) {
            return null;
        }

        $pdo->prepare(
            'UPDATE usuarios
                SET email = :email, email_verificado = 1, email_verificado_em = NOW(), email_pendente = NULL
              WHERE id = :id'
        )->execute([':email' => $novo, ':id' => $id]);

        return (string) $novo;
    }

    /**
     * Confirma a troca de telefone: promove o pendente a definitivo, verifica e
     * atualiza a data. Retorna o novo telefone, ou null se não havia pendente.
     */
    public static function confirmarTelefonePendente(int $id): ?string
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare('SELECT telefone_pendente FROM usuarios WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $novo = $stmt->fetchColumn();
        if (empty($novo)) {
            return null;
        }

        $pdo->prepare(
            'UPDATE usuarios
                SET telefone = :tel, telefone_verificado = 1, telefone_verificado_em = NOW(), telefone_pendente = NULL
              WHERE id = :id'
        )->execute([':tel' => $novo, ':id' => $id]);

        return (string) $novo;
    }

    /**
     * Redefine a senha diretamente (recuperação de senha — sem checar a atual).
     * A autorização é feita antes, via código de verificação validado.
     */
    public static function redefinirSenha(int $id, string $novaSenha): void
    {
        Connection::getConnection()
            ->prepare('UPDATE usuarios SET senha = :senha WHERE id = :id')
            ->execute([':senha' => password_hash($novaSenha, PASSWORD_DEFAULT), ':id' => $id]);
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
            $pdo->prepare('DELETE FROM codigos_verificacao WHERE usuario_id = :id')->execute([':id' => $id]);
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
