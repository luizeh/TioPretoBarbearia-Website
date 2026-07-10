<?php

include_once __DIR__ . '/../config/connection.php';

class DashboardSql
{
    public static function buscarUsuario(int $id): array|false
    {
        $pdo  = Connection::getConnection();
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

        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function contarUsuarios(): int
    {
        $pdo = Connection::getConnection();

        return (int) $pdo
            ->query("SELECT COUNT(*) FROM usuarios WHERE admin = 0")
            ->fetchColumn();
    }

    public static function listarUsuarios(int $limite, int $offset): array
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare("
            SELECT
                id,
                nome,
                sobrenome,
                email,
                telefone,
                cidade
            FROM usuarios
            WHERE admin = 0
            ORDER BY nome
            LIMIT :limite OFFSET :offset
        ");

        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function estatisticas(): array
    {
        $pdo  = Connection::getConnection();
        $hoje = date('Y-m-d');
        $mes  = date('Y-m');

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE admin = 0");
        $stmt->execute();
        $totalClientes = (int) $stmt->fetchColumn();

        $stmt = $pdo->prepare("SELECT COUNT(*) FROM agendamentos WHERE data = :hoje AND status != 'cancelado'");
        $stmt->execute([':hoje' => $hoje]);
        $agendamentosHoje = (int) $stmt->fetchColumn();

        $stmt = $pdo->prepare("
            SELECT COALESCE(SUM(s.preco), 0)
            FROM agendamentos a
            JOIN servicos s ON s.id = a.servico_id
            WHERE DATE_FORMAT(a.data, '%Y-%m') = :mes AND a.status = 'finalizado'
        ");
        $stmt->execute([':mes' => $mes]);
        $receitaMes = (float) $stmt->fetchColumn();

        $mesAnterior = date('Y-m', strtotime('first day of last month'));
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE admin = 0 AND DATE_FORMAT(created_at, '%Y-%m') = :mes");
        $stmt->execute([':mes' => $mes]);
        $novosMes = (int) $stmt->fetchColumn();

        return [
            'total_clientes'    => $totalClientes,
            'agendamentos_hoje' => $agendamentosHoje,
            'receita_mes'       => $receitaMes,
            'novos_mes'         => $novosMes,
        ];
    }
}
