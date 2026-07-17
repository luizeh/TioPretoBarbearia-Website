<div align="center">

# 💈 Tio Preto Barbearia

### Sistema web full-stack para gestão de uma barbearia real — agendamentos, loja e painel administrativo.

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL_8_%2F_MariaDB-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-Vanilla-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![License: MIT](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)

</div>

---

## 📑 Índice

- [Sobre o projeto](#-sobre-o-projeto)
- [Funcionalidades](#-funcionalidades)
- [Tecnologias](#-tecnologias)
- [Arquitetura](#-arquitetura)
- [Estrutura de pastas](#-estrutura-de-pastas)
- [Banco de dados](#-banco-de-dados)
- [Verificação, e-mail e WhatsApp](#-verificação-e-mail-e-whatsapp)
- [Regras de negócio em destaque](#-regras-de-negócio-em-destaque)
- [Segurança](#-segurança)
- [Como rodar localmente](#-como-rodar-localmente)
- [Variáveis de ambiente](#-variáveis-de-ambiente)
- [Convenções do projeto](#-convenções-do-projeto)
- [Licença](#-licença)

---

## 📖 Sobre o projeto

**Tio Preto Barbearia** (Douradina‑PR) é um sistema web que centraliza toda a operação de uma barbearia:

- ✂️ **Agendamento de serviços** pelos clientes, com agenda semanal visual
- 🛒 **Loja de produtos** com carrinho e pedidos
- 🛠️ **Painel administrativo** completo (clientes, serviços, produtos, agenda, horários, relatórios)
- ✉️ **Verificação de e-mail** (SMTP) e 💬 **de telefone / lembretes via WhatsApp** (API externa)
- 📝 **Editor de conteúdo** da landing page e do rodapé

> ⚠️ **Não há integração de pagamento** — os pedidos ficam aguardando confirmação manual do administrador.

---

## ✨ Funcionalidades

### 👤 Área do cliente
- Cadastro e login com senha criptografada (`bcrypt`)
- **Verificação obrigatória de e-mail *e* telefone** por código de 6 dígitos antes de liberar o acesso
- **Cadastro só é gravado no banco após confirmar os dois canais** — cadastros abandonados não ocupam e-mail/telefone (ficam apenas na sessão via `CadastroPendente`)
- **Recuperação de senha** por código enviado ao e-mail
- **Troca de e-mail/telefone** com re-verificação do novo dado (`email_pendente` / `telefone_pendente`)
- Agenda semanal com horários **disponíveis / indisponíveis / fechados**
- Criação, edição e cancelamento dos próprios agendamentos (multi‑serviço)
- Bloqueio de horários que já passaram e de dias fechados
- Carrinho de compras e finalização de pedidos com **endereço de entrega estruturado** (CEP, logradouro, número, bairro…)
- Notificações in-app, edição de perfil e **exclusão da própria conta**

### 🛠️ Área administrativa
- **Dashboard** com estatísticas (clientes, agendamentos do dia, receita, novos no mês)
- **Agenda semanal** com visão de calendário + lista, e envio de lembretes por WhatsApp
- **Agendamento retroativo** — o admin pode registrar atendimentos em datas passadas
- **CRUD** de clientes, serviços, produtos (com upload de imagem) e tags
- **Exclusão lógica** de produtos e serviços (`ativo = 0`): o registro é desativado, preservando o histórico de pedidos/agendamentos
- **Visibilidade de produtos** no site (`visivel`) independente da exclusão lógica
- **Gestão de horários de funcionamento** por dia da semana
- **Bloqueios recorrentes** (ex.: almoço) — inclusive *"todos os dias, exceto X"*
- **Bloqueios por período** (ex.: férias) — dia inteiro ou faixa de horário, com exceções por data
- **Editor do site** (textos da landing page e rodapé)
- **Relatórios** e **trilha de auditoria (logs) paginada**

---

## 🧰 Tecnologias

| Camada              | Tecnologia                                                       |
| ------------------- | ---------------------------------------------------------------- |
| Backend             | **PHP 8.x** (sem framework, MVC simplificado)                    |
| Banco de dados      | **MySQL 8 / MariaDB 10.4** via **PDO** (prepared statements)     |
| Servidor local      | **XAMPP** (MySQL/MariaDB na porta **3307**)                      |
| Frontend            | **HTML5**, **CSS3**, **JavaScript vanilla**                      |
| Alertas / modais    | **SweetAlert2 v11** (via npm)                                    |
| E-mail              | **PHPMailer** (SMTP) — credenciais via `.env`                    |
| Mensagens           | API WhatsApp (r4dev) — credenciais via `.env`                    |
| Ícones              | **Font Awesome 6.5**                                             |
| Fontes              | Google Fonts — Playfair Display, Barlow, Barlow Condensed        |

---

## 🏛️ Arquitetura

Arquitetura **MVC simplificada, sem framework**:

```
Browser ──▶ view/**.php          (páginas renderizadas no servidor)
              │  └─ guard de sessão (require_admin.php / session.php)
              │  └─ controllers/*.controller.php  (prepara dados)
              │        └─ sql/*Sql.php (DAOs) ──▶ config/Connection.php (PDO singleton)
              │
              └─ JS (fetch) ──▶ api/**.php  ──▶ DAOs ──▶ helpers::resposta_json()
```

- **`config/Connection.php`** — conexão PDO (padrão *Singleton*)
- **`config/Env.php`** — carregador simples do `.env` (mantém credenciais fora do código)
- **`sql/*Sql.php`** — DAOs, uma classe por entidade
- **`controllers/*`** — carregam dados e expõem variáveis para as views
- **`helpers/*`** — utilitários (`helpers`), e-mail (`Mailer`), WhatsApp (`Whatsapp`), cadastro pendente (`CadastroPendente`)
- **`api/*`** — endpoints HTTP que **sempre** retornam o mesmo envelope JSON:

```json
{ "success": true, "message": "Texto ao usuário", "data": null }
```

Tratamento de erros por tipo: `InvalidArgumentException` → **422**, `RuntimeException` → **409**, `Throwable` → **500**.

---

## 📂 Estrutura de pastas

```
tiopretobarbearia-crud/
├── index.php                  → redireciona para view/index.php
├── .env.example               → modelo das variáveis de ambiente (SMTP + WhatsApp)
├── config/
│   ├── Connection.php         → conexão PDO (singleton)
│   ├── Env.php                → leitor do .env
│   └── mail.php               → configuração de e-mail (lida do .env)
├── helpers/
│   ├── helpers.php            → validações + resposta_json + guards de sessão
│   ├── Mailer.php             → envio de e-mail (PHPMailer/SMTP) + modo dev
│   ├── Whatsapp.php           → envio de mensagens/códigos via API r4dev
│   └── CadastroPendente.php   → cadastro não confirmado (vive na sessão)
├── sql/
│   ├── *Sql.php               → DAOs (Agendamentos, Clientes, Horarios, Verificacao, ...)
│   └── migrations/
│       └── 000_schema_completo.sql   → schema completo do banco
├── controllers/               → preparam dados para as views
├── api/
│   ├── auth/                  → login, cadastro, verificação e-mail/telefone,
│   │                            recuperação de senha, logout, guards de sessão
│   ├── admin/                 → endpoints do painel (CRUD, horários, whatsapp, site-config)
│   ├── user/                  → endpoints do cliente (agenda, carrinho, pedidos, perfil)
│   └── produtos/              → upload de imagens
├── view/
│   ├── index.php · login.php · cadastro.php · catalogo.php
│   ├── esqueci-senha.php · verificar-email.php · verificar-telefone.php
│   ├── admin/                 → dashboard, agendamentos, horários, produtos, relatórios...
│   ├── user/                  → agenda, carrinho, pedidos, perfil, serviços...
│   └── partials/              → head, sidebar, topbar, paginação e modais reutilizáveis
└── assets/
    ├── css/    (admin · public · auth · shared)
    ├── js/     (admin · auth)
    └── img/produtos/          → imagens enviadas (fora do versionamento — ver .gitignore)
```

---

## 🗄️ Banco de dados

Database: **`tiopretobarbearia`** · Charset `utf8mb4`. Principais tabelas:

| Tabela                   | Descrição                                                                    |
| ------------------------ | ---------------------------------------------------------------------------- |
| `usuarios`               | Clientes e admins (`admin` = 0/1), senha em `bcrypt`, flags de e-mail/telefone verificado e pendências de troca |
| `codigos_verificacao`    | Códigos (hash) de verificação de e-mail/telefone e recuperação de senha      |
| `servicos`               | Serviços oferecidos (preço, duração) — `ativo` p/ exclusão lógica            |
| `agendamentos`           | Agendamentos (com status e observações)                                      |
| `agendamento_servicos`   | Relação N:N — múltiplos serviços por agendamento                             |
| `tags`                   | Categorias de produtos                                                       |
| `produtos`               | Produtos da loja — `visivel` (mostra no site) e `ativo` (exclusão lógica)    |
| `produto_tags`           | Relação N:N produto ↔ tag                                                     |
| `carrinho` / `_itens`    | Carrinho por usuário (1 por usuário)                                         |
| `pedidos` / `_itens`     | Pedidos com endereço estruturado e *snapshot* de nome/preço dos itens        |
| `notificacoes`           | Notificações in-app ao cliente                                               |
| `logs`                   | Trilha de auditoria                                                          |
| `site_config`            | Textos editáveis da landing/rodapé                                           |
| `horarios_funcionamento` | Padrão semanal (1=Seg … 7=Dom, ISO‑8601)                                     |
| `horarios_excecoes`      | Exceção por **data específica**                                              |
| `horarios_bloqueios`     | Bloqueios recorrentes intra‑dia (com exceção de dias)                        |
| `horarios_periodos`      | Bloqueios por **período** (ex.: férias)                                      |

O schema completo (com FKs, índices e *seeds* de `site_config` / `horarios_funcionamento`) está em `sql/migrations/000_schema_completo.sql` — seguro de re-executar (`IF NOT EXISTS`).

---

## 📨 Verificação, e-mail e WhatsApp

O cadastro exige confirmar **e-mail** e **telefone**. Enquanto os dois não são confirmados, os dados ficam só na **sessão** (`helpers/CadastroPendente.php`) — nada é gravado na tabela `usuarios`, então cadastros abandonados não "prendem" e-mails/telefones.

Regras dos códigos (cadastro, verificação e recuperação):

- Código de **6 dígitos**, guardado apenas como **hash** — nunca em texto puro
- **Validade** de 10 min · **cooldown** de 60 s entre reenvios · teto de **5 reenvios/hora** por canal · **5 tentativas** antes de invalidar
- **E-mail** via `helpers/Mailer.php` (PHPMailer/SMTP). Sem `MAIL_HOST` configurado (dev), o e-mail é **gravado em `storage/mail/`** para inspeção em vez de enviado
- **WhatsApp** via `helpers/Whatsapp.php` (API r4dev). Telefone normalizado para `55{DDD}{número}`; credenciais no `.env`, nunca no código

---

## 🎯 Regras de negócio em destaque

- 🕐 **`hora_fim` calculada em SQL** somando a duração dos serviços do agendamento
- 🔒 **Cliente não agenda em horário passado** nem em dias/faixas indisponíveis (o admin pode agendar retroativamente)
- 🧠 **Resolução de disponibilidade por prioridade:**

  ```
  exceção por data  ▸  período (férias)  ▸  padrão semanal
  ```

  Ou seja, uma exceção por data pode **reabrir** um dia dentro das férias.
- 🚫 **Bloqueios flexíveis:** recorrentes (ex.: almoço), *"todos os dias exceto sábado"* e por período (dia inteiro ou faixa de horário)
- 🛒 **Pedidos:** preço e nome do produto são gravados como *snapshot*; o estoque é decrementado na finalização e devolvido no cancelamento
- 🗑️ **Exclusão lógica** de produtos/serviços: o registro é desativado (`ativo = 0`), preservando pedidos e agendamentos históricos
- 🖼️ **Upload seguro de imagens:** valida MIME real (`finfo`), nome aleatório, máx. 2 MB

---

## 🔐 Segurança

- **Senhas:** `password_hash()` / `password_verify()` (bcrypt)
- **Códigos de verificação:** guardados como hash, com expiração, cooldown, limite de reenvios e de tentativas
- **SQL Injection:** PDO com *prepared statements* em todos os DAOs
- **XSS:** `htmlspecialchars` ao renderizar dados nas views
- **Autorização:** guards de sessão (`session.php`, `require_admin.php`, `verificar_login`, `verificar_admin`) antes de qualquer lógica
- **Upload:** validação de MIME real, whitelist de tipos e nome aleatório
- **Credenciais** (SMTP, WhatsApp) e o `.env` ficam **fora do versionamento**

---

## 🚀 Como rodar localmente

### Pré‑requisitos
- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL/MariaDB) — banco na porta **3307**
- [Composer](https://getcomposer.org/) (dependências PHP — PHPMailer)
- [Node.js](https://nodejs.org/) (apenas para instalar o SweetAlert2)

### Passo a passo

```bash
# 1. Clone o repositório dentro de htdocs
git clone <url-do-repo> tiopretobarbearia-crud
cd tiopretobarbearia-crud

# 2. Instale as dependências de front (SweetAlert2)
npm install

# 3. Instale as dependências de back (PHPMailer)
composer install

# 4. Configure as variáveis de ambiente
cp .env.example .env
#   → edite .env e preencha SMTP (MAIL_*) e WhatsApp (WHATSAPP_*).
#   Sem MAIL_HOST, os e-mails são gravados em storage/mail/ (modo dev).
#   Sem WHATSAPP_*, o envio de WhatsApp fica desativado (log de aviso).

# 5. Crie o banco de dados e importe o schema
mysql -h 127.0.0.1 -P 3307 -u root -e "CREATE DATABASE IF NOT EXISTS tiopretobarbearia CHARACTER SET utf8mb4;"
mysql -h 127.0.0.1 -P 3307 -u root tiopretobarbearia < sql/migrations/000_schema_completo.sql
```

> **`.env`, `vendor/`, `storage/` e as imagens de `assets/img/produtos/` não são versionados** (ver `.gitignore`). Rode `composer install` e crie o `.env` a partir do `.env.example` após clonar.

### Acesso

```
🌐 http://localhost/tiopretobarbearia-crud/
```

> **Conexão padrão** (`config/Connection.php`): host `localhost`, porta `3307`, usuário `root`, senha vazia. Ajuste conforme o seu ambiente.

---

## ⚙️ Variáveis de ambiente

Definidas no `.env` (modelo em `.env.example`):

| Variável                                   | Descrição                                                        |
| ------------------------------------------ | ---------------------------------------------------------------- |
| `MAIL_FROM_NAME` / `MAIL_FROM_EMAIL`       | Remetente exibido nos e-mails                                    |
| `MAIL_HOST` / `MAIL_PORT`                  | Servidor SMTP (vazio = modo dev, grava em `storage/mail/`)       |
| `MAIL_ENCRYPTION`                          | `tls` (587) ou `ssl` (465)                                       |
| `MAIL_USERNAME` / `MAIL_PASSWORD`          | Credenciais SMTP                                                 |
| `MAIL_DEBUG`                               | `1` liga o debug SMTP no log do PHP; `0` desliga                 |
| `WHATSAPP_ENDPOINT` / `WHATSAPP_TOKEN`     | Endpoint e token da instância da API r4dev                       |

---

## 📐 Convenções do projeto

- Endpoints de API **sempre** retornam JSON via `helpers::resposta_json()`
- Tratamento de erros por tipo: `InvalidArgumentException` → **422**, `RuntimeException` → **409**, `Throwable` → **500**
- Ações `POST` usam o campo **`action`** no corpo JSON (`criar`, `editar`, `excluir`, `status`, ...)
- Corpo lido com `json_decode(file_get_contents('php://input'), true) ?? []`
- Dias da semana em **ISO‑8601** (1=Segunda … 7=Domingo), compatível com `date('N')`
- Nomenclatura: `view/{recurso}.php` · `api/{área}/{recurso}.php` · `sql/{Entidade}Sql.php` · `controllers/{recurso}.controller.php`

---

## 📄 Licença

Distribuído sob a licença **MIT**. Veja o arquivo [`LICENSE`](LICENSE) para mais detalhes.

<div align="center">

---

Feito para a **Tio Preto Barbearia**

</div>
