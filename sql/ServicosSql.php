<?php

include_once __DIR__ . '/../config/Connection.php';

class ServicosSql
{
    /**
     * Lista serviços ATIVOS. Serviços excluídos logicamente (ativo = 0) não
     * aparecem para novos agendamentos, mas continuam existindo para preservar
     * o histórico de agendamentos antigos.
     */
    public static function listar(): array
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            SELECT id, nome, foto_url, descricao, preco, tempo_estimado
            FROM servicos
            WHERE ativo = 1
            ORDER BY nome
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function buscarPorId(int $id): array|false
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM servicos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function criar(array $dados): int
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO servicos (nome, descricao, preco, tempo_estimado, foto_url)
            VALUES (:nome, :descricao, :preco, :tempo_estimado, :foto_url)
        ");
        $stmt->execute([
            ':nome'           => $dados['nome'],
            ':descricao'      => $dados['descricao'] ?? null,
            ':preco'          => $dados['preco'],
            ':tempo_estimado' => (int) $dados['tempo_estimado'],
            ':foto_url'       => $dados['foto_url'] ?? null,
        ]);
        return (int) $pdo->lastInsertId();
    }

    public static function editar(int $id, array $dados): void
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            UPDATE servicos
            SET nome = :nome, descricao = :descricao, preco = :preco,
                tempo_estimado = :tempo_estimado, foto_url = :foto_url
            WHERE id = :id
        ");
        $stmt->execute([
            ':nome'           => $dados['nome'],
            ':descricao'      => $dados['descricao'] ?? null,
            ':preco'          => $dados['preco'],
            ':tempo_estimado' => (int) $dados['tempo_estimado'],
            ':foto_url'       => $dados['foto_url'] ?? null,
            ':id'             => $id,
        ]);
    }

    /**
     * Conta os agendamentos relacionados a um serviço (via serviço principal ou
     * via multi-serviço), separando os futuros ainda ativos.
     * Retorna ['total' => int, 'futuros' => int].
     */
    public static function contarAgendamentos(int $id): array
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            SELECT
                COUNT(*) AS total,
                COALESCE(SUM(fut), 0) AS futuros
            FROM (
                SELECT a.id,
                    (a.data >= CURDATE() AND a.status IN ('pendente','confirmado')) AS fut
                FROM agendamentos a
                WHERE a.servico_id = :id1
                   OR a.id IN (SELECT agendamento_id FROM agendamento_servicos WHERE servico_id = :id2)
            ) t
        ");
        $stmt->execute([':id1' => $id, ':id2' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'futuros' => 0];
        return ['total' => (int) $row['total'], 'futuros' => (int) $row['futuros']];
    }

    /**
     * Exclui um serviço preservando o histórico de agendamentos.
     *
     *  - Com agendamentos relacionados → EXCLUSÃO LÓGICA (ativo = 0). O serviço
     *    some das listagens e de novos agendamentos, mas os registros antigos
     *    permanecem íntegros. Se houver agendamentos FUTUROS ativos, eles são
     *    cancelados (status = 'cancelado'), preservados como histórico.
     *
     *  - Sem nenhum agendamento → EXCLUSÃO FÍSICA (DELETE).
     *
     * Tudo em transação, com rollback em caso de falha.
     * Retorna ['tipo' => 'logico'|'fisico', 'total' => int, 'futuros' => int, 'cancelados' => int].
     */
    public static function excluir(int $id): array
    {
        $pdo = Connection::getConnection();
        try {
            $pdo->beginTransaction();

            $rel = self::contarAgendamentos($id);

            if ($rel['total'] > 0) {
                // Cancela os agendamentos futuros ainda ativos vinculados ao serviço.
                $cancelados = 0;
                if ($rel['futuros'] > 0) {
                    $upd = $pdo->prepare("
                        UPDATE agendamentos
                        SET status = 'cancelado'
                        WHERE data >= CURDATE()
                          AND status IN ('pendente','confirmado')
                          AND (servico_id = :id1
                               OR id IN (SELECT agendamento_id FROM agendamento_servicos WHERE servico_id = :id2))
                    ");
                    $upd->execute([':id1' => $id, ':id2' => $id]);
                    $cancelados = $upd->rowCount();
                }

                $pdo->prepare("UPDATE servicos SET ativo = 0 WHERE id = :id")->execute([':id' => $id]);
                $pdo->commit();
                return ['tipo' => 'logico', 'total' => $rel['total'], 'futuros' => $rel['futuros'], 'cancelados' => $cancelados];
            }

            // Sem agendamentos → exclusão física.
            $pdo->prepare("DELETE FROM servicos WHERE id = :id")->execute([':id' => $id]);
            $pdo->commit();
            return ['tipo' => 'fisico', 'total' => 0, 'futuros' => 0, 'cancelados' => 0];
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    public static function contar(): int
    {
        $pdo = Connection::getConnection();
        return (int) $pdo->query("SELECT COUNT(*) FROM servicos WHERE ativo = 1")->fetchColumn();
    }
}
