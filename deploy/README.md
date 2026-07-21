# 🚀 Deploy no Railway

Este projeto já vem pronto para subir no [Railway](https://railway.app) via **Dockerfile**
(imagem `php:8.2-apache`, espelhando o ambiente XAMPP local).

## O que já está configurado

| Arquivo | Papel |
| --- | --- |
| `Dockerfile` | PHP 8.2 + Apache + `pdo_mysql`, instala Composer e `node_modules` (SweetAlert2) |
| `.dockerignore` | mantém `vendor/`, `node_modules/`, `.env` e `storage/` fora da imagem (instalados no build) |
| `deploy/apache.conf` | vhost na porta do Railway + bloqueio de execução de scripts na pasta de uploads |
| `deploy/entrypoint.sh` | injeta `$PORT`, garante a pasta de uploads gravável e roda as migrações |
| `deploy/bootstrap.php` | aplica o schema e cria o admin inicial (idempotente) |
| `railway.json` | diz ao Railway para usar o Dockerfile |

## Passo a passo

### 1. Banco de dados
No projeto do Railway: **New → Database → Add MySQL**.

### 2. Serviço da aplicação
**New → GitHub Repo** e selecione este repositório. O Railway detecta o `Dockerfile` sozinho.

### 3. Variáveis de ambiente (serviço da app)
Em **Variables**, defina (use a referência `${{MySQL.VAR}}` — troque `MySQL` pelo nome real do serviço de banco):

```
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_NAME=${{MySQL.MYSQLDATABASE}}
DB_USER=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

# Admin inicial (criado no 1º deploy se ainda não houver admin)
ADMIN_EMAIL=voce@exemplo.com
ADMIN_PASSWORD=umaSenhaForte

# E-mail (SMTP) e WhatsApp — copie do seu .env de produção
MAIL_FROM_NAME=Tio Preto Barbearia
MAIL_FROM_EMAIL=nao-responda@seudominio.com.br
MAIL_HOST=...
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=...
MAIL_PASSWORD=...
WHATSAPP_ENDPOINT=...
WHATSAPP_TOKEN=...
```

> O schema e o admin são criados **automaticamente** no primeiro boot
> (`deploy/bootstrap.php`). Não precisa importar SQL na mão.

### 4. Volume para as imagens (IMPORTANTE)
As fotos de produtos são gravadas em disco. Sem um Volume, elas **somem a cada redeploy**.

No serviço da app: **Settings → Volumes → Add Volume**, com **Mount path**:

```
/var/www/html/assets/img/produtos
```

### 5. Deploy
O Railway builda e publica. Gere um domínio em **Settings → Networking → Generate Domain**.

## Notas

- **Porta:** o Railway injeta `$PORT`; o entrypoint faz o Apache escutar nela (não fixe porta).
- **Sessões** ficam no disco do container (single-instance). Se um dia escalar para várias
  réplicas, migre as sessões para o banco/Redis.
- **Migrar manualmente?** Defina `RUN_MIGRATIONS=0` e importe
  `sql/migrations/000_schema_completo.sql` no banco `railway` você mesmo.
