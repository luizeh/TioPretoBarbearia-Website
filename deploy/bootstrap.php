<?php

/*
 * deploy/bootstrap.php
 * Inicialização idempotente do banco, executada no start do container (Railway).
 *
 *  - Aplica o schema completo (todas as tabelas usam IF NOT EXISTS).
 *  - Cria o admin inicial a partir de ADMIN_EMAIL / ADMIN_PASSWORD, mas
 *    SOMENTE se ainda não existir nenhum admin no banco.
 *
 * É seguro rodar em todo deploy. Nunca derruba o container: qualquer falha
 * é apenas logada (o Apache sobe mesmo assim). Para desligar, defina
 * RUN_MIGRATIONS=0 nas variáveis de ambiente do serviço.
 */

require_once __DIR__ . '/../config/Env.php';

function boot_log(string $msg): void
{
    fwrite(STDERR, "[bootstrap] {$msg}\n");
}

if (Env::get('RUN_MIGRATIONS', '1') !== '1') {
    boot_log('RUN_MIGRATIONS != 1 — migrações puladas.');
    return;
}

$host  = Env::get('DB_HOST', 'localhost');
$port  = Env::get('DB_PORT', '3307');
$nome  = Env::get('DB_NAME', 'tiopretobarbearia');
$user  = Env::get('DB_USER', 'root');
$senha = Env::get('DB_PASSWORD', '');

$dsn = "mysql:host={$host};port={$port};dbname={$nome};charset=utf8mb4";
$opts = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

// O banco pode subir junto com a app — tenta conectar algumas vezes.
$pdo = null;
for ($i = 1; $i <= 10; $i++) {
    try {
        $pdo = new PDO($dsn, $user, $senha, $opts);
        break;
    } catch (Throwable $e) {
        boot_log("tentativa {$i}/10 de conexão falhou: " . $e->getMessage());
        sleep(3);
    }
}

if ($pdo === null) {
    boot_log('não foi possível conectar ao banco — Apache sobe sem migrar.');
    return;
}

// --- 1) Schema -------------------------------------------------------------
try {
    $sqlPath = __DIR__ . '/../sql/migrations/000_schema_completo.sql';
    $sql = file_get_contents($sqlPath);
    if ($sql === false) {
        throw new RuntimeException("schema não encontrado em {$sqlPath}");
    }
    $pdo->exec($sql);
    boot_log('schema aplicado (IF NOT EXISTS — sem efeito se já existia).');
} catch (Throwable $e) {
    boot_log('erro ao aplicar schema: ' . $e->getMessage());
}

// Conexão nova para evitar "commands out of sync" após o exec multi-statement.
try {
    $pdo = new PDO($dsn, $user, $senha, $opts);
} catch (Throwable $e) {
    boot_log('erro ao reconectar para o seed do admin: ' . $e->getMessage());
    return;
}

// --- 1b) Migração idempotente: coluna usuarios.promovido_por --------------
// O CREATE TABLE IF NOT EXISTS não altera tabelas existentes, e o MySQL 8 não
// tem "ADD COLUMN IF NOT EXISTS" — então checamos o information_schema antes.
try {
    $existeCol = (int) $pdo->query(
        "SELECT COUNT(*) FROM information_schema.COLUMNS
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios'
           AND COLUMN_NAME = 'promovido_por'"
    )->fetchColumn();
    if ($existeCol === 0) {
        $pdo->exec('ALTER TABLE usuarios ADD COLUMN promovido_por INT NULL AFTER admin');
        boot_log('coluna promovido_por adicionada em usuarios.');
    }
} catch (Throwable $e) {
    boot_log('erro na migração promovido_por: ' . $e->getMessage());
}

// --- 2) Admin inicial ------------------------------------------------------
try {
    $temAdmin = (int) $pdo->query('SELECT COUNT(*) FROM usuarios WHERE admin = 1')->fetchColumn();

    if ($temAdmin > 0) {
        boot_log('já existe admin — nenhum usuário criado.');
        return;
    }

    $email = Env::get('ADMIN_EMAIL');
    $pass  = Env::get('ADMIN_PASSWORD');

    if (!$email || !$pass) {
        boot_log('sem ADMIN_EMAIL/ADMIN_PASSWORD definidos — admin NÃO criado. '
            . 'Defina essas variáveis e faça redeploy para gerar o primeiro admin.');
        return;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO usuarios
            (nome, sobrenome, email, telefone, senha, cidade,
             admin, email_verificado, telefone_verificado)
         VALUES (?, ?, ?, ?, ?, ?, 1, 1, 1)'
    );
    $stmt->execute([
        Env::get('ADMIN_NOME', 'Admin'),
        Env::get('ADMIN_SOBRENOME', 'Tio Preto'),
        $email,
        Env::get('ADMIN_TELEFONE', '5544000000000'),
        password_hash($pass, PASSWORD_BCRYPT),
        Env::get('ADMIN_CIDADE', 'Douradina-PR'),
    ]);

    boot_log("admin criado: {$email}");
} catch (Throwable $e) {
    boot_log('erro ao semear admin: ' . $e->getMessage());
}
