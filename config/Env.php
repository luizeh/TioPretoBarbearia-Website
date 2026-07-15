<?php

/*
 * Env.php
 * Carregador simples de variáveis de ambiente a partir de um arquivo .env
 * na raiz do projeto. Mantém credenciais sensíveis fora do código-fonte
 * e fora do Git (.env está no .gitignore).
 *
 * Uso:  Env::get('MAIL_HOST', 'localhost');
 *
 * Segue o padrão do projeto: classe estática, carregamento único (cache).
 */

class Env
{
    private static ?array $dados = null;

    private static function carregar(): void
    {
        if (self::$dados !== null) {
            return;
        }

        self::$dados = [];
        $caminho = __DIR__ . '/../.env';

        if (!is_file($caminho) || !is_readable($caminho)) {
            return; // sem .env: get() devolve os defaults
        }

        foreach (file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linha) {
            $linha = trim($linha);
            if ($linha === '' || $linha[0] === '#') {
                continue; // comentário ou linha vazia
            }
            if (!str_contains($linha, '=')) {
                continue;
            }
            [$chave, $valor] = explode('=', $linha, 2);
            $chave = trim($chave);
            $valor = trim($valor);
            // Remove aspas envolventes, se houver
            if (strlen($valor) >= 2 && ($valor[0] === '"' || $valor[0] === "'") && $valor[strlen($valor) - 1] === $valor[0]) {
                $valor = substr($valor, 1, -1);
            }
            self::$dados[$chave] = $valor;
        }
    }

    public static function get(string $chave, ?string $default = null): ?string
    {
        self::carregar();
        $valor = self::$dados[$chave] ?? getenv($chave);
        if ($valor === false || $valor === null || $valor === '') {
            return $default;
        }
        return $valor;
    }
}
