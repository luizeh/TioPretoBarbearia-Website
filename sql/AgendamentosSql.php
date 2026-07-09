<?php

include_once __DIR__ . '/../config/connection.php';

class AgendamentosSql
{
    // ─── Listagem admin (com dados do cliente e serviço) ────────────
    public static function listarTodos(int $limite = 100, int $offset = 0): array
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            SELECT
                a.id,
                CONCAT(u.nome, ' ', u.sobrenome) AS cliente,
                u.telefone,
                s.nome  AS servico,
                s.preco AS preco_servico,
                DATE_FORMAT(a.data, '%d/%m/%Y') AS data_fmt,
                a.data,
                a.hora_inicio,
                a.hora_fim,
                a.status
            FROM agendamentos a
            JOIN usuarios u ON u.id = a.usuario_id
            JOIN servicos s ON s.id = a.servico_id
            ORDER BY a.data DESC, a.hora_inicio DESC
            LIMIT :limite OFFSET :offset
        ");
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─── Listagem para o usuário logado ─────────────────────────────
    public static function listarPorUsuario(int $usuarioId): array
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            SELECT
                a.id,
                s.nome  AS servico,
                s.preco AS preco_servico,
                DATE_FORMAT(a.data, '%d/%m/%Y') AS data_fmt,
                a.data,
                a.hora_inicio,
                a.hora_fim,
                a.status
            FROM agendamentos a
            JOIN servicos s ON s.id = a.servico_id
            WHERE a.usuario_id = :uid
            ORDER BY a.data DESC, a.hora_inicio DESC
        ");
        $stmt->execute([':uid' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─── Listagem paginada para o usuário logado ─────────────────────
    public static function listarPorUsuarioPaginado(int $usuarioId, int $limite, int $offset): array
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            SELECT
                a.id,
                s.nome  AS servico,
                s.preco AS preco_servico,
                DATE_FORMAT(a.data, '%d/%m/%Y') AS data_fmt,
                a.data,
                a.hora_inicio,
                a.hora_fim,
                a.status
            FROM agendamentos a
            JOIN servicos s ON s.id = a.servico_id
            WHERE a.usuario_id = :uid
            ORDER BY a.data DESC, a.hora_inicio DESC
            LIMIT :limite OFFSET :offset
        ");
        $stmt->bindValue(':uid',    $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite,    PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,    PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─── Contar agendamentos do usuário ──────────────────────────────
    public static function contarPorUsuario(int $usuarioId): int
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE usuario_id = :uid");
        $stmt->execute([':uid' => $usuarioId]);
        return (int) $stmt->fetchColumn();
    }

    // ─── Slots ocupados em uma data (para calendário do cliente) ─────
    public static function listarSlotsPorData(string $data, int $usuarioId): array
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            SELECT a.id, a.usuario_id, a.hora_inicio, a.hora_fim, a.status,
                   s.nome AS servico
            FROM agendamentos a
            JOIN servicos s ON s.id = a.servico_id
            WHERE a.data = :data AND a.status NOT IN ('cancelado')
            ORDER BY a.hora_inicio
        ");
        $stmt->execute([':data' => $data]);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Gera slots de 08:00 a 18:00 a cada 30 min
        $slots = [];
        for ($h = 8; $h < 18; $h++) {
            foreach ([0, 30] as $m) {
                $horaSlot   = sprintf('%02d:%02d', $h, $m);
                $horaFull   = $horaSlot . ':00';
                $status     = 'livre';
                $id         = null;
                $servico    = null;
                $ag_status  = null;

                foreach ($appointments as $ag) {
                    if ($ag['hora_inicio'] <= $horaFull && $ag['hora_fim'] > $horaFull) {
                        if ((int) $ag['usuario_id'] === $usuarioId) {
                            $status    = 'meu';
                            $id        = (int) $ag['id'];
                            $servico   = $ag['servico'];
                            $ag_status = $ag['status'];
                        } else {
                            $status = 'ocupado';
                        }
                        break;
                    }
                }

                $slots[] = [
                    'hora'      => $horaSlot,
                    'status'    => $status,
                    'id'        => $id,
                    'servico'   => $servico,
                    'ag_status' => $ag_status,
                ];
            }
        }

        return $slots;
    }
    public static function buscarPorId(int $id): array|false
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            SELECT a.*, CONCAT(u.nome,' ',u.sobrenome) AS cliente,
                   u.telefone, s.nome AS servico
            FROM agendamentos a
            JOIN usuarios u ON u.id = a.usuario_id
            JOIN servicos s ON s.id = a.servico_id
            WHERE a.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ─── Criar ───────────────────────────────────────────────────────
    public static function criar(array $dados): int
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            INSERT INTO agendamentos (usuario_id, servico_id, data, hora_inicio, hora_fim, status)
            VALUES (:usuario_id, :servico_id, :data, :hora_inicio, :hora_fim, :status)
        ");
        $stmt->execute([
            ':usuario_id'  => (int) $dados['usuario_id'],
            ':servico_id'  => (int) $dados['servico_id'],
            ':data'        => $dados['data'],
            ':hora_inicio' => $dados['hora_inicio'],
            ':hora_fim'    => $dados['hora_fim'],
            ':status'      => $dados['status'] ?? 'pendente',
        ]);
        return (int) $pdo->lastInsertId();
    }

    // ─── Editar status ───────────────────────────────────────────────
    public static function editarStatus(int $id, string $status): void
    {
        $allowed = ['pendente', 'confirmado', 'cancelado', 'finalizado'];
        if (!in_array($status, $allowed, true)) {
            return;
        }
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("UPDATE agendamentos SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $status, ':id' => $id]);
    }

    // ─── Editar completo ─────────────────────────────────────────────
    public static function editar(int $id, array $dados): void
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            UPDATE agendamentos
            SET servico_id = :servico_id, data = :data,
                hora_inicio = :hora_inicio, hora_fim = :hora_fim, status = :status
            WHERE id = :id
        ");
        $stmt->execute([
            ':servico_id'  => (int) $dados['servico_id'],
            ':data'        => $dados['data'],
            ':hora_inicio' => $dados['hora_inicio'],
            ':hora_fim'    => $dados['hora_fim'],
            ':status'      => $dados['status'],
            ':id'          => $id,
        ]);
    }

    // ─── Excluir ─────────────────────────────────────────────────────
    public static function excluir(int $id): void
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("DELETE FROM agendamentos WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    // ─── Estatísticas dashboard ──────────────────────────────────────
    public static function estatisticas(): array
    {
        $pdo  = Connection::getConnection();
        $hoje = date('Y-m-d');

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE data = :hoje");
        $stmt->execute([':hoje' => $hoje]);
        $hoje_count = (int) $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE status = 'confirmado'");
        $stmt->execute();
        $confirmados = (int) $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE status = 'pendente'");
        $stmt->execute();
        $pendentes = (int) $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE status = 'cancelado'");
        $stmt->execute();
        $cancelados = (int) $stmt->fetchColumn();

        return [
            'hoje'        => $hoje_count,
            'confirmados' => $confirmados,
            'pendentes'   => $pendentes,
            'cancelados'  => $cancelados,
        ];
    }

    // ─── Próximos agendamentos (hoje em diante) ──────────────────────
    public static function proximosAgendamentos(int $limite = 10): array
    {
        $pdo  = Connection::getConnection();
        $hoje = date('Y-m-d');
        $stmt = $pdo->prepare("
            SELECT
                CONCAT(u.nome, ' ', u.sobrenome) AS cliente,
                s.nome  AS servico,
                a.hora_inicio,
                a.status
            FROM agendamentos a
            JOIN usuarios u ON u.id = a.usuario_id
            JOIN servicos s ON s.id = a.servico_id
            WHERE a.data >= :hoje AND a.status != 'cancelado'
            ORDER BY a.data ASC, a.hora_inicio ASC
            LIMIT :limite
        ");
        $stmt->bindValue(':hoje', $hoje);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ─── Receita do mês ──────────────────────────────────────────────
    public static function receitaMes(): float
    {
        $pdo  = Connection::getConnection();
        $mes  = date('Y-m');
        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(s.preco), 0)
            FROM agendamentos a
            JOIN servicos s ON s.id = a.servico_id
            WHERE DATE_FORMAT(a.data, '%Y-%m') = :mes
              AND a.status = 'finalizado'
        ");
        $stmt->execute([':mes' => $mes]);
        return (float) $stmt->fetchColumn();
    }
}
