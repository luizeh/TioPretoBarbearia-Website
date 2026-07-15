<div align="center">

# 💈 Tio Preto Barbearia

### Sistema web full-stack para gestão de uma barbearia real — agendamentos, loja e painel administrativo.

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
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
- [Regras de negócio em destaque](#-regras-de-negócio-em-destaque)
- [Como rodar localmente](#-como-rodar-localmente)
- [Convenções do projeto](#-convenções-do-projeto)
- [Licença](#-licença)

---

## 📖 Sobre o projeto

**Tio Preto Barbearia** (Douradina‑PR) é um sistema web que centraliza toda a operação de uma barbearia:

- ✂️ **Agendamento de serviços** pelos clientes, com agenda semanal visual
- 🛒 **Loja de produtos** com carrinho e pedidos
- 🛠️ **Painel administrativo** completo (clientes, serviços, produtos, agenda, horários)
- 💬 **Lembretes via WhatsApp** (API externa)
- 📝 **Editor de conteúdo** da landing page e do rodapé

> ⚠️ **Não há integração de pagamento** — os pedidos ficam aguardando confirmação manual do administrador.

---

## ✨ Funcionalidades

### 👤 Área do cliente
- Cadastro e login com senha criptografada (`bcrypt`)
- Agenda semanal com horários **disponíveis / indisponíveis / fechados**
- Criação, edição e cancelamento dos próprios agendamentos (multi‑serviço)
- Bloqueio de horários que já passaram e de dias fechados
- Carrinho de compras e finalização de pedidos
- Notificações e edição de perfil

### 🛠️ Área administrativa
- **Dashboard** com estatísticas (clientes, agendamentos do dia, receita, etc.)
- **Agenda semanal** com visão de calendário + lista, e envio de lembretes
- **CRUD** de clientes, serviços, produtos (com upload de imagem) e tags
- **Gestão de horários de funcionamento** por dia da semana
- **Bloqueios recorrentes** (ex.: almoço) — inclusive *"todos os dias, exceto X"*
- **Bloqueios por período** (ex.: férias) — dia inteiro ou faixa de horário, com exceções por data
- **Editor do site** (textos da landing page e rodapé)

---

## 🧰 Tecnologias

| Camada              | Tecnologia                                                       |
| ------------------- | ---------------------------------------------------------------- |
| Backend             | **PHP** (sem framework, MVC simplificado)                        |
| Banco de dados      | **MySQL 8** via **PDO** (prepared statements)                    |
| Servidor local      | **XAMPP** (MySQL na porta **3307**)                              |
| Frontend            | **HTML5**, **CSS3**, **JavaScript vanilla**                      |
| Alertas / modais    | **SweetAlert2 v11** (via npm)                                    |
| Ícones              | **Font Awesome 6.5**                                             |
| Fontes              | Google Fonts — Playfair Display, Barlow, Barlow Condensed        |
| Mensagens           | API WhatsApp (r4dev)                                             |

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
- **`sql/*Sql.php`** — DAOs, uma classe por entidade
- **`controllers/*`** — carregam dados e expõem variáveis para as views
- **`api/*`** — endpoints HTTP que **sempre** retornam o mesmo envelope JSON:

```json
{ "success": true, "message": "Texto ao usuário", "data": null }
```

---

## 📂 Estrutura de pastas

```
tiopretobarbearia-crud/
├── index.php                  → redireciona para view/index.php
├── config/
│   └── Connection.php         → conexão PDO (singleton)
├── helpers/
│   └── helpers.php            → validações + resposta_json + guards
├── sql/
│   ├── *Sql.php               → DAOs (Agendamentos, Clientes, Horarios, ...)
│   └── migrations/
│       └── 000_schema_completo.sql   → schema completo do banco
├── controllers/               → preparam dados para as views
├── api/
│   ├── auth/                  → login, cadastro, logout, guards de sessão
│   ├── admin/                 → endpoints do painel (CRUD, horários, whatsapp)
│   ├── user/                  → endpoints do cliente (agenda, carrinho, pedidos)
│   └── produtos/              → upload de imagens
├── view/
│   ├── index.php · login.php · cadastro.php · catalogo.php
│   ├── admin/                 → dashboard, agendamentos, horários, produtos...
│   ├── user/                  → agenda, carrinho, pedidos, perfil...
│   └── partials/              → head, sidebar, topbar, modais reutilizáveis
└── assets/
    ├── css/    (admin · public · auth · shared)
    ├── js/     (admin · public · auth)
    └── img/produtos/          → imagens enviadas
```

---

## 🗄️ Banco de dados

Database: **`tiopretobarbearia`** · Charset `utf8mb4`. Principais tabelas:

| Tabela                   | Descrição                                                        |
| ------------------------ | ---------------------------------------------------------------- |
| `usuarios`               | Clientes e admins (`admin` = 0/1), senha em `bcrypt`             |
| `servicos`               | Serviços oferecidos (preço, duração)                             |
| `agendamentos`           | Agendamentos (com status e observações)                          |
| `agendamento_servicos`   | Relação N:N — múltiplos serviços por agendamento                 |
| `produtos` / `tags`      | Produtos da loja e categorias                                    |
| `carrinho` / `_itens`    | Carrinho por usuário                                             |
| `pedidos` / `_itens`     | Pedidos com *snapshot* de preço                                  |
| `notificacoes` / `logs`  | Notificações ao cliente e trilha de auditoria                    |
| `site_config`            | Textos editáveis da landing/rodapé                               |
| `horarios_funcionamento` | Padrão semanal (1=Seg … 7=Dom, ISO‑8601)                         |
| `horarios_excecoes`      | Exceção por **data específica**                                  |
| `horarios_bloqueios`     | Bloqueios recorrentes intra‑dia (com exceção de dias)            |
| `horarios_periodos`      | Bloqueios por **período** (ex.: férias)                          |

---

## 🎯 Regras de negócio em destaque

- 🕐 **`hora_fim` calculada em SQL** somando a duração dos serviços do agendamento
- 🔒 **Cliente não agenda em horário passado** nem em dias/faixas indisponíveis
- 🧠 **Resolução de disponibilidade por prioridade:**

  ```
  exceção por data  ▸  período (férias)  ▸  padrão semanal
  ```

  Ou seja, uma exceção por data pode **reabrir** um dia dentro das férias.
- 🚫 **Bloqueios flexíveis:** recorrentes (ex.: almoço), *"todos os dias exceto sábado"* e por período (dia inteiro ou faixa de horário)
- 🖼️ **Upload seguro de imagens:** valida MIME real (`finfo`), nome aleatório, máx. 2 MB
- 🔐 **Segurança:** PDO com *prepared statements*, `htmlspecialchars` nas views, guards de sessão e validação de input em todas as camadas

---

## 🚀 Como rodar localmente

### Pré‑requisitos
- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL) — MySQL configurado na porta **3307**
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

# 4. Configure as variáveis de ambiente (credenciais SMTP)
cp .env.example .env
#   → edite .env e preencha MAIL_HOST, MAIL_USERNAME, MAIL_PASSWORD, etc.
#   Sem SMTP configurado (MAIL_HOST vazio), os e-mails são gravados em
#   storage/mail/ para inspeção durante o desenvolvimento.

# 5. Crie o banco de dados e importe o schema
mysql -h 127.0.0.1 -P 3307 -u root -e "CREATE DATABASE IF NOT EXISTS tiopretobarbearia CHARACTER SET utf8mb4;"
mysql -h 127.0.0.1 -P 3307 -u root tiopretobarbearia < sql/migrations/000_schema_completo.sql
```

> **`.env` e `vendor/` não são versionados** (ver `.gitignore`). Rode `composer install` e crie o `.env` a partir do `.env.example` após clonar.

### Acesso

```
🌐 http://localhost/tiopretobarbearia-crud/
```

> **Conexão padrão** (`config/Connection.php`): host `localhost`, porta `3307`, usuário `root`, senha vazia. Ajuste conforme o seu ambiente.

---

## 📐 Convenções do projeto

- Endpoints de API **sempre** retornam JSON via `helpers::resposta_json()`
- Tratamento de erros por tipo: `InvalidArgumentException` → **422**, `RuntimeException` → **409**, `Throwable` → **500**
- Ações `POST` usam o campo **`action`** no corpo JSON (`criar`, `editar`, `excluir`, `status`, ...)
- Dias da semana em **ISO‑8601** (1=Segunda … 7=Domingo), compatível com `date('N')`
- Nomenclatura: `view/{recurso}.php` · `api/{área}/{recurso}.php` · `sql/{Entidade}Sql.php` · `controllers/{recurso}.controller.php`

---

## 📄 Licença

Distribuído sob a licença **MIT**. Veja o arquivo [`LICENSE`](LICENSE) para mais detalhes.

<div align="center">

---

Feito para a **Tio Preto Barbearia**

</div>
