<?php

/*
 * Connection.php
 * Responsável por criar e retornar a conexão com o banco de dados.
 *
 * Padrão DAO: esta classe é usada pelos DAOs para obter a conexão PDO.
 * Chamada: $pdo = Connection::getConnection();
 */

class Connection
{

    // Guarda a conexão para não abrir uma nova a cada chamada
    private static $conn = null;

    private function __construct() {}

    public static function getConnection()
    {
        if (self::$conn === null) {
            self::$conn = new PDO(
                "mysql:host=localhost;port=3307;dbname=tiopretobarbearia;charset=utf8mb4",
                "root",   // usuário do banco
                "",       // senha (vazio no XAMPP padrão)
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // lança erros como exceções
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC        // retorna arrays associativos
                ]
            );
        }

        return self::$conn;
    }
}
