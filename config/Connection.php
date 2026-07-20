<?php

/*
 * Connection.php
 * Responsável por criar e retornar a conexão com o banco de dados.
 *
 * Padrão DAO: esta classe é usada pelos DAOs para obter a conexão PDO.
 * Chamada: $pdo = Connection::getConnection();
 */

require_once __DIR__ . '/Env.php';

class Connection
{

    // Guarda a conexão para não abrir uma nova a cada chamada
    private static $conn = null;

    private function __construct() {}

    public static function getConnection()
    {
        if (self::$conn === null) {
            // Credenciais vêm do ambiente (Env::get lê o .env local ou, na
            // ausência dele, as variáveis de ambiente — ex.: Railway).
            // Os defaults mantêm o XAMPP funcionando sem configuração extra.
            $host = Env::get('DB_HOST', 'localhost');
            $port = Env::get('DB_PORT', '3307');
            $nome = Env::get('DB_NAME', 'tiopretobarbearia');
            $user = Env::get('DB_USER', 'root');
            $senha = Env::get('DB_PASSWORD', '');

            self::$conn = new PDO(
                "mysql:host={$host};port={$port};dbname={$nome};charset=utf8mb4",
                $user,
                $senha,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // lança erros como exceções
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC        // retorna arrays associativos
                ]
            );
        }

        return self::$conn;
    }
}
