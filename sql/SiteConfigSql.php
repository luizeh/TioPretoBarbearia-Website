<?php

include_once __DIR__ . '/../config/connection.php';

class SiteConfigSql
{
    /** Cache de request para não repetir queries na mesma execução PHP */
    private static array $cache = [];

    /**
     * Retorna todos os itens agrupados por grupo.
     * Formato: [ 'landing' => [ ['chave', 'valor', 'titulo', 'grupo'], ... ], 'footer' => [...] ]
     */
    public static function buscarTodos(): array
    {
        $pdo  = Connection::getConnection();
        $rows = $pdo->query(
            'SELECT chave, valor, titulo, grupo FROM site_config ORDER BY grupo, chave'
        )->fetchAll(PDO::FETCH_ASSOC);

        $grupos = [];
        foreach ($rows as $row) {
            $grupos[$row['grupo']][]    = $row;
            self::$cache[$row['chave']] = $row['valor'];
        }
        return $grupos;
    }

    /**
     * Retorna mapa chave→valor de um grupo específico.
     * Popula o cache para uso posterior via get().
     */
    public static function buscarGrupo(string $grupo): array
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare(
            'SELECT chave, valor FROM site_config WHERE grupo = :grupo'
        );
        $stmt->execute([':grupo' => $grupo]);

        $mapa = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $mapa[$row['chave']]        = $row['valor'];
            self::$cache[$row['chave']] = $row['valor'];
        }
        return $mapa;
    }

    /**
     * Busca uma chave com fallback seguro.
     * Usa cache se já carregado por buscarGrupo() ou chamada anterior.
     */
    public static function get(string $chave, string $fallback = ''): string
    {
        if (array_key_exists($chave, self::$cache)) {
            return self::$cache[$chave];
        }

        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare('SELECT valor FROM site_config WHERE chave = :chave');
        $stmt->execute([':chave' => $chave]);
        $valor = $stmt->fetchColumn();

        self::$cache[$chave] = $valor !== false ? (string) $valor : $fallback;
        return self::$cache[$chave];
    }

    /**
     * Atualiza o valor de uma chave existente.
     * rowCount() não é usado: MySQL retorna 0 quando o valor não muda,
     * o que causaria falso positivo de "chave não encontrada".
     */
    public static function salvar(string $chave, string $valor): void
    {
        $pdo  = Connection::getConnection();
        $stmt = $pdo->prepare(
            'UPDATE site_config SET valor = :valor WHERE chave = :chave'
        );
        $stmt->execute([':valor' => $valor, ':chave' => $chave]);
        self::$cache[$chave] = $valor;
    }
}
