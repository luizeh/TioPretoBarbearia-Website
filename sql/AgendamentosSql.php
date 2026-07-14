<?php

include_once __DIR__ . '/../config/connection.php';
include_once __DIR__ . '/HorariosSql.php';

class AgendamentosSql
{
    private static function camposAgendamento(): string
    {
        return "
            a.id, a.usuario_id, a.servico_id, a.data, a.hora_inicio,
            ADDTIME(a.hora_inicio, SEC_TO_TIME(COALESCE(SUM(si.tempo_estimado), s.tempo_estimado) * 60)) AS hora_fim,
            a.status, a.observacoes,
            CONCAT(u.nome, ' ', u.sobrenome) AS cliente, u.telefone,
            COALESCE(GROUP_CONCAT(DISTINCT aps.servico_id ORDER BY aps.servico_id SEPARATOR ','), a.servico_id) AS servicos_ids,
            COALESCE(NULLIF(GROUP_CONCAT(DISTINCT si.nome ORDER BY si.nome SEPARATOR ', '), ''), s.nome) AS servico,
            COALESCE(SUM(si.preco), s.preco) AS preco_servico,
            COALESCE(SUM(si.tempo_estimado), s.tempo_estimado) AS duracao_minutos,
            DATE_FORMAT(a.data, '%d/%m/%Y') AS data_fmt
        ";
    }

    private static function joinsAgendamento(): string
    {
        return "
            FROM agendamentos a
            JOIN usuarios u ON u.id = a.usuario_id
            JOIN servicos s ON s.id = a.servico_id
            LEFT JOIN agendamento_servicos aps ON aps.agendamento_id = a.id
            LEFT JOIN servicos si ON si.id = aps.servico_id
        ";
    }

    private static function agruparAgendamento(): string
    {
        return ' GROUP BY a.id ';
    }

    public static function listarTodos(int $limite = 100, int $offset = 0): array
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare('SELECT ' . self::camposAgendamento() . self::joinsAgendamento()
            . self::agruparAgendamento() . ' ORDER BY a.data DESC, a.hora_inicio DESC LIMIT :limite OFFSET :offset');
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarPorPeriodo(string $dataInicio, string $dataFim): array
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare('SELECT ' . self::camposAgendamento() . self::joinsAgendamento()
            . ' WHERE a.data BETWEEN :inicio AND :fim ' . self::agruparAgendamento() . ' ORDER BY a.data, a.hora_inicio');
        $stmt->execute([':inicio' => $dataInicio, ':fim' => $dataFim]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarPorUsuario(int $usuarioId): array
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare('SELECT ' . self::camposAgendamento() . self::joinsAgendamento()
            . ' WHERE a.usuario_id = :uid ' . self::agruparAgendamento() . ' ORDER BY a.data DESC, a.hora_inicio DESC');
        $stmt->execute([':uid' => $usuarioId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarPorUsuarioPaginado(int $usuarioId, int $limite, int $offset): array
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare('SELECT ' . self::camposAgendamento() . self::joinsAgendamento()
            . ' WHERE a.usuario_id = :uid ' . self::agruparAgendamento() . ' ORDER BY a.data DESC, a.hora_inicio DESC LIMIT :limite OFFSET :offset');
        $stmt->bindValue(':uid', $usuarioId, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function contarPorUsuario(int $usuarioId): int
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM agendamentos WHERE usuario_id = :uid');
        $stmt->execute([':uid' => $usuarioId]);
        return (int) $stmt->fetchColumn();
    }

    public static function buscarPorId(int $id): array|false
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare('SELECT ' . self::camposAgendamento() . self::joinsAgendamento()
            . ' WHERE a.id = :id ' . self::agruparAgendamento());
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function listarServicosDoAgendamento(int $agendamentoId): array
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare("
            SELECT s.id, s.nome, s.preco, s.tempo_estimado
            FROM agendamento_servicos aps
            JOIN servicos s ON s.id = aps.servico_id
            WHERE aps.agendamento_id = :id
            ORDER BY s.nome
        ");
        $stmt->execute([':id' => $agendamentoId]);
        $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($servicos) {
            return $servicos;
        }

        $stmt = $pdo->prepare('SELECT id, nome, preco, tempo_estimado FROM servicos WHERE id = (SELECT servico_id FROM agendamentos WHERE id = :id)');
        $stmt->execute([':id' => $agendamentoId]);
        $servico = $stmt->fetch(PDO::FETCH_ASSOC);
        return $servico ? [$servico] : [];
    }

    public static function listarAgendaPrivada(string $inicio, string $fim, int $usuarioId): array
    {
        $agendamentos = self::listarPorPeriodo($inicio, $fim);
        return array_map(static function (array $agendamento) use ($usuarioId): array {
            $proprio = (int) $agendamento['usuario_id'] === $usuarioId;
            return [
                'id' => $proprio ? (int) $agendamento['id'] : null,
                'data' => $agendamento['data'],
                'hora_inicio' => $agendamento['hora_inicio'],
                'hora_fim' => $agendamento['hora_fim'],
                'duracao_minutos' => (int) ($agendamento['duracao_minutos'] ?? 30),
                'status' => $proprio ? $agendamento['status'] : 'ocupado',
                'proprio' => $proprio,
                'servico' => $proprio ? $agendamento['servico'] : null,
            ];
        }, array_filter($agendamentos, static fn(array $agendamento): bool => $agendamento['status'] !== 'cancelado'));
    }

    public static function calcularDadosServicos(array $servicosIds): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $servicosIds), static fn(int $id): bool => $id > 0)));
        if (!$ids) {
            throw new InvalidArgumentException('Selecione pelo menos um serviço.');
        }

        $pdo = Connection::getConnection();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $pdo->prepare("SELECT id, nome, preco, tempo_estimado FROM servicos WHERE id IN ($placeholders)");
        $stmt->execute($ids);
        $servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($servicos) !== count($ids)) {
            throw new InvalidArgumentException('Um ou mais serviços selecionados não existem.');
        }

        $duracao = array_sum(array_map(static fn(array $s): int => (int) $s['tempo_estimado'], $servicos));
        return ['ids' => $ids, 'duracao' => $duracao, 'servicos' => $servicos];
    }

    private static function validarPeriodo(string $data, string $horaInicio, int $duracao, bool $bloquearPassado = false): array
    {
        $inicio = DateTime::createFromFormat('Y-m-d H:i', "$data $horaInicio");
        $erros = DateTime::getLastErrors();
        if (!$inicio || ($erros !== false && ($erros['warning_count'] || $erros['error_count']))) {
            throw new InvalidArgumentException('Data ou horário inválidos.');
        }
        if ($bloquearPassado) {
            // Cliente: não pode agendar em um horário que já passou (inclui horários de hoje).
            if ($inicio < new DateTime()) {
                throw new InvalidArgumentException('Não é possível agendar em um horário que já passou. Escolha um horário futuro.');
            }
        } elseif ($inicio < new DateTime('today')) {
            throw new InvalidArgumentException('Não é possível agendar em uma data passada.');
        }
        $fim = (clone $inicio)->modify("+$duracao minutes");

        // Resolve o horário efetivo da data com a prioridade
        // exceção por data > período (dia inteiro) > padrão semanal.
        $dia = HorariosSql::resolverDia($data);
        if ($dia['fechado']) {
            throw new InvalidArgumentException(
                $dia['motivo']
                    ? "A barbearia está fechada neste dia ({$dia['motivo']})."
                    : 'A barbearia não atende neste dia.'
            );
        }
        $abertura   = substr($dia['abertura'],   0, 5);
        $fechamento = substr($dia['fechamento'], 0, 5);

        if ($inicio->format('H:i') < $abertura || $fim->format('H:i') > $fechamento) {
            throw new InvalidArgumentException(
                "O atendimento deve ocorrer entre {$abertura} e {$fechamento}."
            );
        }

        // Bloqueios (recorrentes + faixas de período) já resolvidos para esta data.
        foreach ($dia['bloqueios'] as $bloqueio) {
            $bInicio = substr($bloqueio['hora_inicio'], 0, 5);
            $bFim    = substr($bloqueio['hora_fim'],    0, 5);
            // Há sobreposição se o agendamento começa antes do fim do bloqueio E termina depois do início
            if ($inicio->format('H:i') < $bFim && $fim->format('H:i') > $bInicio) {
                $desc = $bloqueio['descricao'] ? " ({$bloqueio['descricao']})" : '';
                throw new InvalidArgumentException(
                    "Horário indisponível{$desc}: {$bInicio}–{$bFim}."
                );
            }
        }

        return [$inicio->format('H:i:s'), $fim->format('H:i:s')];
    }

    private static function validarStatus(string $status): string
    {
        $status = trim($status);
        $permitidos = ['pendente', 'confirmado', 'cancelado', 'finalizado'];
        if (!in_array($status, $permitidos, true)) {
            throw new InvalidArgumentException('Status inválido.');
        }

        return $status;
    }

    private static function validarConflito(PDO $pdo, string $data, string $inicio, string $fim, ?int $ignorarId = null): void
    {
        $sql = "SELECT a.id
                FROM agendamentos a
                JOIN servicos s ON s.id = a.servico_id
                LEFT JOIN agendamento_servicos aps ON aps.agendamento_id = a.id
                LEFT JOIN servicos si ON si.id = aps.servico_id
                WHERE a.data = :data AND a.status <> 'cancelado'
                AND a.hora_inicio < :fim";
        $params = [':data' => $data, ':inicio' => $inicio, ':fim' => $fim];
        if ($ignorarId) {
            $sql .= ' AND a.id <> :id';
            $params[':id'] = $ignorarId;
        }
        $stmt = $pdo->prepare($sql . ' GROUP BY a.id HAVING ADDTIME(MIN(a.hora_inicio), SEC_TO_TIME(COALESCE(SUM(si.tempo_estimado), MAX(s.tempo_estimado)) * 60)) > :inicio FOR UPDATE');
        $stmt->execute($params);
        if ($stmt->fetchColumn()) {
            throw new RuntimeException('Este horário não está mais disponível. Escolha outro horário.');
        }
    }

    public static function salvarComServicos(array $dados, ?int $id = null, ?int $usuarioId = null, bool $bloquearPassado = false): int
    {
        $data = trim((string) ($dados['data'] ?? ''));
        $horaInicio = trim((string) ($dados['hora_inicio'] ?? ''));
        $idsServicos = $dados['servicos_ids'] ?? [];
        if (!$idsServicos && !empty($dados['servico_id'])) {
            $idsServicos = [(int) $dados['servico_id']];
        }
        $servicos = self::calcularDadosServicos($idsServicos);
        [$inicio, $fim] = self::validarPeriodo($data, $horaInicio, $servicos['duracao'], $bloquearPassado);
        $status = null;
        if ($id === null) {
            if ((int) ($dados['usuario_id'] ?? 0) <= 0) {
                throw new InvalidArgumentException('Cliente inválido.');
            }
            $status = self::validarStatus((string) ($dados['status'] ?? 'pendente'));
        } elseif (array_key_exists('status', $dados)) {
            $status = self::validarStatus((string) $dados['status']);
        }
        $pdo = Connection::getConnection();

        try {
            $pdo->beginTransaction();
            self::validarConflito($pdo, $data, $inicio, $fim, $id);
            $principal = $servicos['ids'][0];
            $observacoes = trim((string) ($dados['observacoes'] ?? '')) ?: null;

            if ($id === null) {
                $stmt = $pdo->prepare("INSERT INTO agendamentos (usuario_id, servico_id, data, hora_inicio, hora_fim, status, observacoes)
                    VALUES (:usuario_id, :servico_id, :data, :hora_inicio, :hora_fim, :status, :observacoes)");
                $stmt->execute([
                    ':usuario_id' => (int) $dados['usuario_id'],
                    ':servico_id' => $principal,
                    ':data' => $data,
                    ':hora_inicio' => $inicio,
                    ':hora_fim' => $fim,
                    ':status' => $status,
                    ':observacoes' => $observacoes,
                ]);
                $id = (int) $pdo->lastInsertId();
            } else {
                $where = 'id = :id';
                $params = [':id' => $id];
                if ($usuarioId !== null) {
                    $where .= ' AND usuario_id = :usuario_id';
                    $params[':usuario_id'] = $usuarioId;
                }
                $ownership = $pdo->prepare("SELECT id FROM agendamentos WHERE $where FOR UPDATE");
                $ownership->execute($params);
                if (!$ownership->fetchColumn()) {
                    throw new RuntimeException('Agendamento não encontrado.');
                }
                $stmt = $pdo->prepare("UPDATE agendamentos SET servico_id = :servico_id, data = :data, hora_inicio = :hora_inicio,
                    hora_fim = :hora_fim, observacoes = :observacoes" . ($status !== null ? ', status = :status' : '') . " WHERE $where");
                $params = array_merge($params, [
                    ':servico_id' => $principal,
                    ':data' => $data,
                    ':hora_inicio' => $inicio,
                    ':hora_fim' => $fim,
                    ':observacoes' => $observacoes,
                ]);
                if ($status !== null) $params[':status'] = $status;
                $stmt->execute($params);
                $pdo->prepare('DELETE FROM agendamento_servicos WHERE agendamento_id = :id')->execute([':id' => $id]);
            }

            $stmtItem = $pdo->prepare('INSERT INTO agendamento_servicos (agendamento_id, servico_id) VALUES (:agendamento_id, :servico_id)');
            foreach ($servicos['ids'] as $servicoId) {
                $stmtItem->execute([':agendamento_id' => $id, ':servico_id' => $servicoId]);
            }
            $pdo->commit();
            return $id;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }

    public static function editarStatus(int $id, string $status): void
    {
        $status = self::validarStatus($status);
        $pdo = Connection::getConnection();
        $pdo->prepare('UPDATE agendamentos SET status = :status WHERE id = :id')->execute([':status' => $status, ':id' => $id]);
    }

    public static function cancelarPorUsuario(int $id, int $usuarioId): bool
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare("UPDATE agendamentos SET status = 'cancelado' WHERE id = :id AND usuario_id = :usuario_id");
        $stmt->execute([':id' => $id, ':usuario_id' => $usuarioId]);
        return $stmt->rowCount() > 0;
    }

    public static function excluirPorUsuario(int $id, int $usuarioId): bool
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare('DELETE FROM agendamentos WHERE id = :id AND usuario_id = :usuario_id');
        $stmt->execute([':id' => $id, ':usuario_id' => $usuarioId]);
        return $stmt->rowCount() > 0;
    }

    public static function excluir(int $id): void
    {
        $pdo = Connection::getConnection();
        $pdo->prepare('DELETE FROM agendamentos WHERE id = :id')->execute([':id' => $id]);
    }

    public static function estatisticas(): array
    {
        $pdo = Connection::getConnection();
        $hoje = date('Y-m-d');
        $dados = [];
        foreach (['hoje' => "data = :hoje", 'confirmados' => "status = 'confirmado'", 'pendentes' => "status = 'pendente'", 'cancelados' => "status = 'cancelado'"] as $chave => $where) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE $where");
            $stmt->execute($chave === 'hoje' ? [':hoje' => $hoje] : []);
            $dados[$chave] = (int) $stmt->fetchColumn();
        }
        return $dados;
    }

    public static function proximosAgendamentos(int $limite = 10): array
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare('SELECT ' . self::camposAgendamento() . self::joinsAgendamento()
            . " WHERE a.data >= :hoje AND a.status <> 'cancelado' " . self::agruparAgendamento() . ' ORDER BY a.data, a.hora_inicio LIMIT :limite');
        $stmt->bindValue(':hoje', date('Y-m-d'));
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function receitaMes(): float
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT COALESCE(SUM(COALESCE(itens.total, s.preco)), 0)
            FROM agendamentos a JOIN servicos s ON s.id = a.servico_id
            LEFT JOIN (SELECT aps.agendamento_id, SUM(si.preco) AS total FROM agendamento_servicos aps JOIN servicos si ON si.id = aps.servico_id GROUP BY aps.agendamento_id) itens ON itens.agendamento_id = a.id
            WHERE DATE_FORMAT(a.data, '%Y-%m') = :mes AND a.status = 'finalizado'");
        $stmt->execute([':mes' => date('Y-m')]);
        return (float) $stmt->fetchColumn();
    }
}
