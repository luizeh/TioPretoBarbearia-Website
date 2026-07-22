<?php

/*
 * DbSessionHandler.php
 * Armazena as sessões PHP no banco (tabela `sessoes`) em vez de arquivos em
 * /tmp. No Railway o /tmp é apagado a cada deploy, o que derrubava o login;
 * o banco é persistente, então a sessão sobrevive a redeploys.
 *
 * Registrado em helpers::iniciarSessao() antes de session_start().
 */

require_once __DIR__ . '/../config/Connection.php';

class DbSessionHandler implements SessionHandlerInterface
{
    private int $ttl;

    public function __construct(int $ttl)
    {
        $this->ttl = $ttl;
    }

    private function pdo(): PDO
    {
        return Connection::getConnection();
    }

    public function open($path, $name): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    // Todas as operações são resilientes: em caso de falha no banco (ex.: tabela
    // ainda não criada, indisponibilidade momentânea) elas degradam para um
    // resultado seguro em vez de lançar exceção e derrubar a página inteira.

    #[\ReturnTypeWillChange]
    public function read($id)
    {
        try {
            $stmt = $this->pdo()->prepare('SELECT payload FROM sessoes WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? (string) $row['payload'] : '';
        } catch (Throwable $e) {
            error_log('DbSessionHandler read: ' . $e->getMessage());
            return '';
        }
    }

    public function write($id, $data): bool
    {
        try {
            $stmt = $this->pdo()->prepare(
                'INSERT INTO sessoes (id, payload, ultimo_acesso)
                 VALUES (:id, :payload, :t)
                 ON DUPLICATE KEY UPDATE payload = VALUES(payload), ultimo_acesso = VALUES(ultimo_acesso)'
            );
            return $stmt->execute([':id' => $id, ':payload' => $data, ':t' => time()]);
        } catch (Throwable $e) {
            error_log('DbSessionHandler write: ' . $e->getMessage());
            return false;
        }
    }

    public function destroy($id): bool
    {
        try {
            $stmt = $this->pdo()->prepare('DELETE FROM sessoes WHERE id = :id');
            return $stmt->execute([':id' => $id]);
        } catch (Throwable $e) {
            error_log('DbSessionHandler destroy: ' . $e->getMessage());
            return false;
        }
    }

    #[\ReturnTypeWillChange]
    public function gc($maxlifetime)
    {
        try {
            $stmt = $this->pdo()->prepare('DELETE FROM sessoes WHERE ultimo_acesso < :limite');
            $stmt->execute([':limite' => time() - $this->ttl]);
            return $stmt->rowCount();
        } catch (Throwable $e) {
            error_log('DbSessionHandler gc: ' . $e->getMessage());
            return 0;
        }
    }
}
