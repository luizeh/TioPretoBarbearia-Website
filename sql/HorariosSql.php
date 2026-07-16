<?php

include_once __DIR__ . '/../config/Connection.php';

/**
 * HorariosSql — DAO para a tabela horarios_funcionamento.
 *
 * dia_semana usa ISO-8601: 1=Segunda … 6=Sábado, 7=Domingo
 * (compatível com PHP date('N') e com o padrão da tabela existente)
 */
class HorariosSql
{
    private static array $cache = [];

    /** Nomes dos dias para exibição no painel */
    public static function nomesDias(): array
    {
        return [
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado',
            7 => 'Domingo',
        ];
    }

    /**
     * Retorna todos os 7 registros indexados por dia_semana.
     * Se algum dia não tiver registro no banco, retorna fallback fechado.
     */
    public static function buscarTodos(): array
    {
        $pdo  = Connection::getConnection();
        $rows = $pdo->query(
            'SELECT id, dia_semana, abertura, fechamento, fechado
             FROM horarios_funcionamento
             ORDER BY dia_semana'
        )->fetchAll(PDO::FETCH_ASSOC);

        $mapa = [];
        foreach ($rows as $row) {
            $dia = (int) $row['dia_semana'];
            $mapa[$dia] = [
                'id'         => (int) $row['id'],
                'dia_semana' => $dia,
                'abertura'   => $row['abertura'],
                'fechamento' => $row['fechamento'],
                'fechado'    => (bool) $row['fechado'],
            ];
            self::$cache[$dia] = $mapa[$dia];
        }
        return $mapa;
    }

    /**
     * Retorna o horário de um dia específico (ISO 1–7).
     * Usa cache para evitar queries repetidas na mesma requisição.
     */
    public static function buscarDia(int $diaSemana): array
    {
        if (isset(self::$cache[$diaSemana])) {
            return self::$cache[$diaSemana];
        }

        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare(
            'SELECT id, dia_semana, abertura, fechamento, fechado
             FROM horarios_funcionamento
             WHERE dia_semana = :dia'
        );
        $stmt->execute([':dia' => $diaSemana]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fallback: dia sem configuração no banco → trata como aberto com horário padrão
        if (!$row) {
            self::$cache[$diaSemana] = [
                'id'         => 0,
                'dia_semana' => $diaSemana,
                'abertura'   => '08:00:00',
                'fechamento' => '20:00:00',
                'fechado'    => false,
            ];
            return self::$cache[$diaSemana];
        }

        self::$cache[$diaSemana] = [
            'id'         => (int) $row['id'],
            'dia_semana' => (int) $row['dia_semana'],
            'abertura'   => $row['abertura'],
            'fechamento' => $row['fechamento'],
            'fechado'    => (bool) $row['fechado'],
        ];
        return self::$cache[$diaSemana];
    }

    /** Padrão de um dia-da-semana a partir do mapa buscarTodos(), com fallback aberto. */
    private static function padraoDia(int $diaSemana, array $todos): array
    {
        return $todos[$diaSemana] ?? [
            'id'         => 0,
            'dia_semana' => $diaSemana,
            'abertura'   => '08:00:00',
            'fechamento' => '20:00:00',
            'fechado'    => false,
        ];
    }

    /**
     * Resolve o horário efetivo de UMA data a partir dos dados já carregados.
     * Prioridade: exceção por data > período (dia inteiro) > padrão semanal.
     * Faixas de bloqueio (recorrentes + faixas de período) são somadas por cima.
     *
     * @param array      $padrao         padrão do dia-da-semana
     * @param array|null $excecao        exceção da data (ou null)
     * @param array      $periodos       períodos que cobrem esta data
     * @param array      $todosBloqueios bloqueios recorrentes
     */
    private static function montarHorario(string $date, array $padrao, ?array $excecao, array $periodos, array $todosBloqueios): array
    {
        $diaSemana  = (int) date('N', strtotime($date)); // 1=Seg … 7=Dom
        $fechado    = (bool) $padrao['fechado'];
        $abertura   = $padrao['abertura'];
        $fechamento = $padrao['fechamento'];
        $motivo     = null;
        $temExcecao = $excecao !== null;

        if ($temExcecao) {
            // Exceção por data tem prioridade máxima — pode reabrir um dia dentro de um período.
            $fechado    = (bool) $excecao['fechado'];
            $abertura   = $excecao['abertura']   ?? $padrao['abertura'];
            $fechamento = $excecao['fechamento'] ?? $padrao['fechamento'];
        } else {
            // Período de dia inteiro (hora_inicio NULL) fecha a data.
            foreach ($periodos as $p) {
                if ($p['hora_inicio'] === null && $p['data_inicio'] <= $date && $date <= $p['data_fim']) {
                    $fechado = true;
                    $motivo  = $p['descricao'] ?: 'Período bloqueado';
                    break;
                }
            }
        }

        // Faixas de bloqueio sobre o horário aberto: recorrentes do dia + faixas de período.
        $bloqueios = [];
        foreach ($todosBloqueios as $b) {
            if ($b['dia_semana'] !== null) {
                if ((int) $b['dia_semana'] === $diaSemana) {
                    $bloqueios[] = $b;
                }
            } elseif (!in_array($diaSemana, $b['dias_excecao'] ?? [], true)) {
                // "Todos os dias", exceto os dias listados em dias_excecao
                $bloqueios[] = $b;
            }
        }
        foreach ($periodos as $p) {
            if ($p['hora_inicio'] !== null && $p['data_inicio'] <= $date && $date <= $p['data_fim']) {
                $bloqueios[] = [
                    'id'          => 'p' . $p['id'],
                    'dia_semana'  => null,
                    'hora_inicio' => $p['hora_inicio'],
                    'hora_fim'    => $p['hora_fim'],
                    'descricao'   => $p['descricao'],
                ];
            }
        }

        return [
            'id'         => $padrao['id'],
            'dia_semana' => $diaSemana,
            'abertura'   => $abertura,
            'fechamento' => $fechamento,
            'fechado'    => $fechado,
            'excecao'    => $temExcecao,
            'motivo'     => $motivo,
            'bloqueios'  => array_values($bloqueios),
        ];
    }

    /**
     * Retorna mapa [data Y-m-d => horario] para um array de datas.
     * Cada entrada inclui 'fechado', 'abertura', 'fechamento', 'excecao',
     * 'motivo' (descrição do período quando fechado por período) e 'bloqueios'.
     */
    public static function buscarPorDatas(array $dates): array
    {
        if (!$dates) return [];
        $todos          = self::buscarTodos();
        $excecoes       = self::buscarExcecoes($dates);
        $todosBloqueios = self::buscarTodosBloqueios();
        $periodos       = self::buscarPeriodosPorIntervalo(min($dates), max($dates));

        $resultado = [];
        foreach ($dates as $date) {
            $diaSemana = (int) date('N', strtotime($date));
            $resultado[$date] = self::montarHorario(
                $date,
                self::padraoDia($diaSemana, $todos),
                $excecoes[$date] ?? null,
                $periodos,
                $todosBloqueios
            );
        }
        return $resultado;
    }

    /**
     * Resolve o horário efetivo de uma única data (usa as mesmas regras de buscarPorDatas).
     * Usado pela validação de agendamento.
     */
    public static function resolverDia(string $data): array
    {
        return self::montarHorario(
            $data,
            self::padraoDia((int) date('N', strtotime($data)), self::buscarTodos()),
            self::buscarExcecoes([$data])[$data] ?? null,
            self::buscarPeriodosPorIntervalo($data, $data),
            self::buscarTodosBloqueios()
        );
    }

    /**
     * Retorna mapa [data Y-m-d => excecao] para um conjunto de datas.
     */
    public static function buscarExcecoes(array $dates): array
    {
        if (!$dates) return [];
        $pdo          = Connection::getConnection();
        $placeholders = implode(',', array_fill(0, count($dates), '?'));
        $stmt         = $pdo->prepare(
            "SELECT data, fechado, abertura, fechamento
             FROM horarios_excecoes
             WHERE data IN ($placeholders)"
        );
        $stmt->execute($dates);
        $mapa = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $mapa[$row['data']] = $row;
        }
        return $mapa;
    }

    /**
     * Cria ou atualiza uma exceção para uma data específica.
     */
    public static function salvarExcecao(string $data, bool $fechado, ?string $abertura, ?string $fechamento): void
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO horarios_excecoes (data, fechado, abertura, fechamento)
             VALUES (:data, :fechado, :abertura, :fechamento)
             ON DUPLICATE KEY UPDATE
                fechado    = VALUES(fechado),
                abertura   = VALUES(abertura),
                fechamento = VALUES(fechamento)'
        );
        $stmt->execute([
            ':data'      => $data,
            ':fechado'   => (int) $fechado,
            ':abertura'  => $abertura,
            ':fechamento' => $fechamento,
        ]);
    }

    /**
     * Remove a exceção de uma data (restaura o padrão do dia-da-semana).
     */
    public static function excluirExcecao(string $data): void
    {
        $pdo = Connection::getConnection();
        $pdo->prepare('DELETE FROM horarios_excecoes WHERE data = :data')
            ->execute([':data' => $data]);
    }

    /**
     * Retorna todos os bloqueios recorrentes.
     * dia_semana NULL = aplica a todos os dias.
     * Retorna [] se a tabela ainda não existir no banco.
     */
    public static function buscarTodosBloqueios(): array
    {
        try {
            $pdo  = Connection::getConnection();
            $rows = $pdo->query(
                'SELECT id, dia_semana, dias_excecao, hora_inicio, hora_fim, descricao
                 FROM horarios_bloqueios
                 ORDER BY ISNULL(dia_semana), dia_semana, hora_inicio'
            )->fetchAll(PDO::FETCH_ASSOC);

            return array_map(static fn(array $r): array => [
                'id'           => (int) $r['id'],
                'dia_semana'   => $r['dia_semana'] !== null ? (int) $r['dia_semana'] : null,
                'dias_excecao' => ($r['dias_excecao'] ?? '') !== ''
                    ? array_map('intval', explode(',', $r['dias_excecao']))
                    : [],
                'hora_inicio'  => $r['hora_inicio'],
                'hora_fim'     => $r['hora_fim'],
                'descricao'    => $r['descricao'],
            ], $rows);
        } catch (PDOException $e) {
            // Tabela ainda não criada — retorna vazio sem quebrar a página
            return [];
        }
    }

    /**
     * Cria um bloqueio recorrente.
     */
    public static function criarBloqueio(?int $diaSemana, string $horaInicio, string $horaFim, ?string $descricao, ?string $diasExcecao = null): int
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO horarios_bloqueios (dia_semana, dias_excecao, hora_inicio, hora_fim, descricao)
             VALUES (:dia, :excecao, :inicio, :fim, :desc)'
        );
        $stmt->execute([':dia' => $diaSemana, ':excecao' => $diasExcecao, ':inicio' => $horaInicio, ':fim' => $horaFim, ':desc' => $descricao]);
        return (int) $pdo->lastInsertId();
    }

    /**
     * Atualiza um bloqueio recorrente existente.
     */
    public static function editarBloqueio(int $id, ?int $diaSemana, string $horaInicio, string $horaFim, ?string $descricao, ?string $diasExcecao = null): void
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare(
            'UPDATE horarios_bloqueios
                SET dia_semana = :dia, dias_excecao = :excecao, hora_inicio = :inicio, hora_fim = :fim, descricao = :desc
              WHERE id = :id'
        );
        $stmt->execute([':dia' => $diaSemana, ':excecao' => $diasExcecao, ':inicio' => $horaInicio, ':fim' => $horaFim, ':desc' => $descricao, ':id' => $id]);
    }

    /**
     * Remove um bloqueio pelo ID.
     */
    public static function excluirBloqueio(int $id): void
    {
        $pdo = Connection::getConnection();
        $pdo->prepare('DELETE FROM horarios_bloqueios WHERE id = :id')->execute([':id' => $id]);
    }

    // ── Períodos (bloqueios por intervalo de datas — ex.: férias) ────────

    /** Mapeia uma linha de horarios_periodos para o formato usado no app. */
    private static function mapearPeriodo(array $r): array
    {
        return [
            'id'          => (int) $r['id'],
            'data_inicio' => $r['data_inicio'],
            'data_fim'    => $r['data_fim'],
            'hora_inicio' => $r['hora_inicio'],
            'hora_fim'    => $r['hora_fim'],
            'descricao'   => $r['descricao'],
        ];
    }

    /**
     * Todos os períodos cadastrados (para o painel admin).
     * Retorna [] se a tabela ainda não existir.
     */
    public static function buscarTodosPeriodos(): array
    {
        try {
            $pdo  = Connection::getConnection();
            $rows = $pdo->query(
                'SELECT id, data_inicio, data_fim, hora_inicio, hora_fim, descricao
                 FROM horarios_periodos
                 ORDER BY data_inicio, data_fim'
            )->fetchAll(PDO::FETCH_ASSOC);
            return array_map([self::class, 'mapearPeriodo'], $rows);
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Períodos que se sobrepõem ao intervalo [inicio, fim] (datas Y-m-d).
     * Retorna [] se a tabela ainda não existir.
     */
    public static function buscarPeriodosPorIntervalo(string $inicio, string $fim): array
    {
        try {
            $pdo  = Connection::getConnection();
            $stmt = $pdo->prepare(
                'SELECT id, data_inicio, data_fim, hora_inicio, hora_fim, descricao
                 FROM horarios_periodos
                 WHERE data_inicio <= :fim AND data_fim >= :inicio
                 ORDER BY data_inicio'
            );
            $stmt->execute([':inicio' => $inicio, ':fim' => $fim]);
            return array_map([self::class, 'mapearPeriodo'], $stmt->fetchAll(PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Cria um período. hora_inicio/hora_fim NULL = fecha o dia inteiro no intervalo.
     */
    public static function criarPeriodo(string $dataInicio, string $dataFim, ?string $horaInicio, ?string $horaFim, ?string $descricao): int
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO horarios_periodos (data_inicio, data_fim, hora_inicio, hora_fim, descricao)
             VALUES (:di, :df, :hi, :hf, :desc)'
        );
        $stmt->execute([':di' => $dataInicio, ':df' => $dataFim, ':hi' => $horaInicio, ':hf' => $horaFim, ':desc' => $descricao]);
        return (int) $pdo->lastInsertId();
    }

    /**
     * Atualiza um período existente.
     */
    public static function editarPeriodo(int $id, string $dataInicio, string $dataFim, ?string $horaInicio, ?string $horaFim, ?string $descricao): void
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare(
            'UPDATE horarios_periodos
                SET data_inicio = :di, data_fim = :df, hora_inicio = :hi, hora_fim = :hf, descricao = :desc
              WHERE id = :id'
        );
        $stmt->execute([':di' => $dataInicio, ':df' => $dataFim, ':hi' => $horaInicio, ':hf' => $horaFim, ':desc' => $descricao, ':id' => $id]);
    }

    /**
     * Remove um período pelo ID.
     */
    public static function excluirPeriodo(int $id): void
    {
        $pdo = Connection::getConnection();
        $pdo->prepare('DELETE FROM horarios_periodos WHERE id = :id')->execute([':id' => $id]);
    }

    /**
     * Atualiza o horário de um dia (usa UPSERT para garantir existência).
     */
    public static function salvar(int $diaSemana, string $abertura, string $fechamento, bool $fechado): void
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO horarios_funcionamento (dia_semana, abertura, fechamento, fechado)
             VALUES (:dia, :abertura, :fechamento, :fechado)
             ON DUPLICATE KEY UPDATE
                abertura   = VALUES(abertura),
                fechamento = VALUES(fechamento),
                fechado    = VALUES(fechado)'
        );
        $stmt->execute([
            ':dia'       => $diaSemana,
            ':abertura'  => $abertura,
            ':fechamento' => $fechamento,
            ':fechado'   => (int) $fechado,
        ]);

        // Invalida cache deste dia
        unset(self::$cache[$diaSemana]);
    }
}
