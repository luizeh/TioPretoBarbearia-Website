<?php

include_once __DIR__ . '/../config/connection.php';

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

    /**
     * Retorna mapa [data Y-m-d => horario] para um array de datas.
     * Exceções de data específica sobrepõem o padrão do dia-da-semana.
     * Cada entrada inclui 'bloqueios': [[hora_inicio, hora_fim], ...]
     */
    public static function buscarPorDatas(array $dates): array
    {
        $todos    = self::buscarTodos();
        $excecoes = self::buscarExcecoes($dates);
        $todosBloqueios = self::buscarTodosBloqueios();
        $resultado = [];
        foreach ($dates as $date) {
            $diaSemana = (int) date('N', strtotime($date)); // 1=Seg … 7=Dom
            $padrao = $todos[$diaSemana] ?? [
                'id'         => 0,
                'dia_semana' => $diaSemana,
                'abertura'   => '08:00:00',
                'fechamento' => '20:00:00',
                'fechado'    => false,
            ];

            if (isset($excecoes[$date])) {
                $ex = $excecoes[$date];
                $horario = [
                    'id'         => $padrao['id'],
                    'dia_semana' => $diaSemana,
                    'abertura'   => $ex['abertura']   ?? $padrao['abertura'],
                    'fechamento' => $ex['fechamento'] ?? $padrao['fechamento'],
                    'fechado'    => (bool) $ex['fechado'],
                    'excecao'    => true,
                ];
            } else {
                $horario = array_merge($padrao, ['excecao' => false]);
            }

            // Agrega bloqueios aplicáveis a este dia
            $horario['bloqueios'] = array_values(array_filter(
                $todosBloqueios,
                static fn(array $b): bool => $b['dia_semana'] === null || (int) $b['dia_semana'] === $diaSemana
            ));
            $resultado[$date] = $horario;
        }
        return $resultado;
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
                'SELECT id, dia_semana, hora_inicio, hora_fim, descricao
                 FROM horarios_bloqueios
                 ORDER BY ISNULL(dia_semana), dia_semana, hora_inicio'
            )->fetchAll(PDO::FETCH_ASSOC);

            return array_map(static fn(array $r): array => [
                'id'          => (int) $r['id'],
                'dia_semana'  => $r['dia_semana'] !== null ? (int) $r['dia_semana'] : null,
                'hora_inicio' => $r['hora_inicio'],
                'hora_fim'    => $r['hora_fim'],
                'descricao'   => $r['descricao'],
            ], $rows);
        } catch (PDOException $e) {
            // Tabela ainda não criada — retorna vazio sem quebrar a página
            return [];
        }
    }

    /**
     * Cria um bloqueio recorrente.
     */
    public static function criarBloqueio(?int $diaSemana, string $horaInicio, string $horaFim, ?string $descricao): int
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare(
            'INSERT INTO horarios_bloqueios (dia_semana, hora_inicio, hora_fim, descricao)
             VALUES (:dia, :inicio, :fim, :desc)'
        );
        $stmt->execute([':dia' => $diaSemana, ':inicio' => $horaInicio, ':fim' => $horaFim, ':desc' => $descricao]);
        return (int) $pdo->lastInsertId();
    }

    /**
     * Remove um bloqueio pelo ID.
     */
    public static function excluirBloqueio(int $id): void
    {
        $pdo = Connection::getConnection();
        $pdo->prepare('DELETE FROM horarios_bloqueios WHERE id = :id')->execute([':id' => $id]);
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
