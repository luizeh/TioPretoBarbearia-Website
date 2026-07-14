# Contexto Geral — Tio Preto Barbearia (Sistema CRUD)

> Documento de referência completo para ser passado a outra IA. Cobre arquitetura, banco de dados, API, convenções e regras de negócio de todo o projeto.

---

## 1. Visão Geral

Sistema web full-stack para gerenciamento de uma barbearia real ("Tio Preto Barbearia", Douradina-PR). O projeto é acadêmico/prático e centraliza:

- Agendamento de serviços pelos clientes
- Loja de produtos com carrinho e pedidos
- Painel administrativo completo
- Comunicação via WhatsApp (API externa)
- Editor de conteúdo da landing page

**Não há integração de pagamento** — pedidos ficam aguardando confirmação manual do administrador.

---

## 2. Stack Tecnológica

| Camada            | Tecnologia                                                              |
| ----------------- | ----------------------------------------------------------------------- |
| Linguagem backend | PHP (sem framework)                                                     |
| Banco de dados    | MySQL 8 via PDO                                                         |
| Servidor local    | XAMPP (porta MySQL: **3307**, não 3306)                                 |
| Database          | `tiopretobarbearia`                                                     |
| Frontend          | HTML5, CSS3, JavaScript vanilla                                         |
| Alertas/modais JS | SweetAlert2 v11 (instalado via npm em `node_modules/`)                  |
| Ícones            | Font Awesome 6.5 (CDN)                                                  |
| Fontes            | Google Fonts: Playfair Display, Barlow, Barlow Condensed                |
| API de mensagens  | r4dev WhatsApp API (HTTPS, token hardcoded em `api/admin/whatsapp.php`) |

---

## 3. Arquitetura Geral

O projeto usa uma arquitetura **MVC simplificada sem framework**:

```
index.php                   → redireciona para view/index.php
config/Connection.php       → Singleton PDO (padrão DAO)
helpers/helpers.php         → classe utilitária estática
sql/*Sql.php                → DAOs (Data Access Objects), 1 classe por entidade
controllers/*.controller.php → prepara dados para as views (inclui sessão + DAOs)
api/                        → endpoints HTTP que retornam JSON
view/                       → páginas HTML renderizadas no servidor
assets/                     → CSS, JS, imagens
```

### Fluxo de uma requisição típica (View + Controller)

```
Browser → view/admin/agendamentos.php
         → require api/auth/require_admin.php  (guard: redireciona se não logado/admin)
         → include controllers/agendamentos.controller.php  (busca dados via DAOs)
         → renderiza HTML com dados embutidos
         → JS chama api/admin/agendamentos.php via fetch() para ações AJAX
```

### Fluxo de uma requisição de API (AJAX)

```
fetch('../../api/admin/produtos.php', { method: 'POST', body: JSON })
         → require session_admin.php  (guard)
         → chama método do DAO (ProdutosSql::*)
         → helpers::resposta_json(success, message, data, httpCode)
```

---

## 4. Estrutura de Pastas

```
/
├── index.php                    → redireciona para view/index.php
├── package.json                 → { "dependencies": { "sweetalert2": "^11" } }
├── readme.md                    → documento de visão do projeto
├── context.md                   → ESTE ARQUIVO
│
├── config/
│   └── Connection.php           → singleton PDO
│
├── helpers/
│   └── helpers.php              → classe helpers (static): resposta_json, verificar_login,
│                                   verificar_admin, validarTexto, validarEmail,
│                                   validarTelefone, normalizarTelefone, validarSenha
│
├── sql/                         → DAOs (uma classe por tabela/domínio)
│   ├── AgendamentosSql.php
│   ├── CarrinhoSql.php
│   ├── ClientesSql.php
│   ├── DashboardSql.php
│   ├── HorariosSql.php
│   ├── LogsSql.php
│   ├── NotificacoesSql.php
│   ├── PedidosSql.php
│   ├── ProdutosSql.php
│   ├── ServicosSql.php
│   ├── SiteConfigSql.php
│   ├── UsuariosSql.php
│   └── migrations/
│       └── 000_schema_completo.sql
│
├── controllers/                 → preparam variáveis para as views
│   ├── agendamentos.controller.php
│   ├── clientes.controller.php
│   ├── dashboard.controller.php
│   ├── produtos.controller.php
│   └── servicos.controller.php
│
├── api/
│   ├── auth/
│   │   ├── login.php            → POST action=login
│   │   ├── logout.php           → destrói sessão
│   │   ├── cadastro.php         → POST action=cadastro
│   │   ├── session.php          → guard: redireciona para login.php se não logado
│   │   └── require_admin.php    → guard: redireciona se não logado ou não admin
│   ├── admin/
│   │   ├── session_admin.php    → guard admin (inclui session.php)
│   │   ├── agendamentos.php     → CRUD agendamentos + logs + notificações
│   │   ├── clientes.php         → listagem/edição de clientes
│   │   ├── dashboard.php        → estatísticas + logs
│   │   ├── horarios.php         → horários de funcionamento
│   │   ├── pedidos.php          → gestão de pedidos
│   │   ├── produtos.php         → CRUD de produtos + tags
│   │   ├── servicos.php         → CRUD de serviços
│   │   ├── site-config.php      → editor de conteúdo da landing/footer
│   │   ├── tags.php             → CRUD de tags
│   │   └── whatsapp.php         → envio de mensagens WhatsApp
│   ├── produtos/
│   │   └── upload-foto.php      → upload de imagem de produto (max 2MB, JPG/PNG/WebP/GIF)
│   └── user/
│       ├── agendamentos.php     → CRUD agendamentos do próprio usuário
│       ├── carrinho.php         → gestão do carrinho
│       ├── notificacoes.php     → listagem + marcar como lida
│       ├── pedidos.php          → finalizar pedido + histórico
│       └── perfil.php           → editar perfil
│
├── view/
│   ├── index.php                → landing page pública
│   ├── login.php                → página de login
│   ├── cadastro.php             → página de cadastro
│   ├── catalogo.php             → catálogo de produtos (público)
│   ├── admin/                   → painel administrativo
│   │   ├── dashboard.php
│   │   ├── agendamentos.php
│   │   ├── clientes.php
│   │   ├── horarios.php
│   │   ├── pedidos.php
│   │   ├── produtos.php
│   │   ├── relatorios.php
│   │   ├── servicos.php
│   │   └── site-editor.php
│   ├── user/                    → área do cliente logado
│   │   ├── agendamentos.php
│   │   ├── carrinho.php
│   │   ├── notificacoes.php
│   │   ├── pedidos.php
│   │   ├── perfil.php
│   │   └── servicos.php
│   └── partials/
│       ├── head.php             → <head> painel admin (CSS admin + SweetAlert2)
│       ├── head_public.php      → <head> área pública
│       ├── sidebar.php          → menu lateral admin
│       ├── topbar.php           → barra superior admin
│       ├── footer.php           → rodapé público
│       ├── header_public.php    → cabeçalho público
│       ├── pagination.php       → componente de paginação
│       ├── scripts.php          → scripts compartilhados
│       └── modais/              → modais reutilizáveis
│
└── assets/
    ├── css/
    │   ├── admin/               → app.css, base.css, layout.css, pages.css
    │   ├── auth/                → login.css, cadastro.css
    │   ├── public/              → tokens.css, app.css, home.css, catalog.css,
    │   │                           commerce.css, header.css, footer.css,
    │   │                           cart-menu.css, user.css, responsive.css, etc.
    │   └── shared/              → agenda.css, components.css
    ├── img/
    │   └── produtos/            → imagens de produtos (upload dinâmico)
    └── js/
        ├── admin/               → agendamentos.js, clientes.js, dashboard.js,
        │                           horarios.js, image-upload.js, layout.js,
        │                           modal-handler.js, pedidos.js, produtos.js,
        │                           servicos.js, site-editor.js, table-filters.js, tags.js
        └── auth/                → login.js, cadastro.js
```

---

## 5. Banco de Dados — Conexão

**Arquivo:** `config/Connection.php`  
**Padrão:** Singleton estático

```php
Connection::getConnection();
// mysql:host=localhost;port=3307;dbname=tiopretobarbearia;charset=utf8mb4
// user: root | senha: (vazia)
// PDO::ATTR_ERRMODE => ERRMODE_EXCEPTION
// PDO::ATTR_DEFAULT_FETCH_MODE => FETCH_ASSOC
```

---

## 6. Schema Completo do Banco de Dados

### Tabela `usuarios`

| Coluna     | Tipo                | Obs                        |
| ---------- | ------------------- | -------------------------- |
| id         | INT PK AI           |                            |
| nome       | VARCHAR(100)        |                            |
| sobrenome  | VARCHAR(100)        |                            |
| email      | VARCHAR(150) UNIQUE | identificador da conta     |
| telefone   | VARCHAR(20) UNIQUE  | armazenado sem formatação  |
| senha      | VARCHAR(255)        | hash com `password_hash()` |
| cidade     | VARCHAR(100)        |                            |
| admin      | TINYINT(1)          | 0 = cliente, 1 = admin     |
| created_at | DATETIME            | DEFAULT CURRENT_TIMESTAMP  |

### Tabela `servicos`

| Coluna         | Tipo              | Obs                |
| -------------- | ----------------- | ------------------ |
| id             | INT PK AI         |                    |
| nome           | VARCHAR(150)      |                    |
| descricao      | TEXT NULL         |                    |
| preco          | DECIMAL(10,2)     |                    |
| tempo_estimado | INT               | duração em minutos |
| foto_url       | VARCHAR(500) NULL |                    |

### Tabela `agendamentos`

| Coluna      | Tipo            | Obs                                              |
| ----------- | --------------- | ------------------------------------------------ |
| id          | INT PK AI       |                                                  |
| usuario_id  | INT FK→usuarios | CASCADE                                          |
| servico_id  | INT FK→servicos | RESTRICT (serviço principal)                     |
| data        | DATE            |                                                  |
| hora_inicio | TIME            |                                                  |
| hora_fim    | TIME            | calculado dinamicamente via SQL                  |
| status      | ENUM            | 'pendente','confirmado','cancelado','finalizado' |
| observacoes | TEXT NULL       |                                                  |

### Tabela `agendamento_servicos` (multi-serviço)

| Coluna         | Tipo                         | Obs      |
| -------------- | ---------------------------- | -------- |
| agendamento_id | INT FK→agendamentos          | CASCADE  |
| servico_id     | INT FK→servicos              | RESTRICT |
| PK             | (agendamento_id, servico_id) |          |

> Um agendamento pode ter múltiplos serviços. `agendamento.servico_id` é o serviço principal (legado/fallback). Os serviços extras ficam em `agendamento_servicos`.

### Tabela `tags`

| Coluna | Tipo               | Obs |
| ------ | ------------------ | --- |
| id     | INT PK AI          |     |
| nome   | VARCHAR(80) UNIQUE |     |

### Tabela `produtos`

| Coluna    | Tipo              | Obs |
| --------- | ----------------- | --- |
| id        | INT PK AI         |     |
| nome      | VARCHAR(150)      |     |
| descricao | TEXT NULL         |     |
| preco     | DECIMAL(10,2)     |     |
| estoque   | INT               |     |
| foto_url  | VARCHAR(500) NULL |     |

### Tabela `produto_tags` (N:N)

| Coluna     | Tipo                    |
| ---------- | ----------------------- |
| produto_id | INT FK→produtos CASCADE |
| tag_id     | INT FK→tags CASCADE     |
| PK         | (produto_id, tag_id)    |

### Tabela `carrinho`

| Coluna     | Tipo                   | Obs                    |
| ---------- | ---------------------- | ---------------------- |
| id         | INT PK AI              |                        |
| usuario_id | INT FK→usuarios UNIQUE | 1 carrinho por usuário |
| created_at | DATETIME               |                        |

### Tabela `carrinho_itens`

| Coluna      | Tipo                      | Obs                      |
| ----------- | ------------------------- | ------------------------ |
| id          | INT PK AI                 |                          |
| carrinho_id | INT FK→carrinho CASCADE   |                          |
| produto_id  | INT FK→produtos CASCADE   |                          |
| quantidade  | INT                       |                          |
| UNIQUE      | (carrinho_id, produto_id) | sem duplicata de produto |

### Tabela `pedidos`

| Coluna      | Tipo                    | Obs                                                     |
| ----------- | ----------------------- | ------------------------------------------------------- |
| id          | INT PK AI               |                                                         |
| usuario_id  | INT FK→usuarios CASCADE |                                                         |
| endereco    | TEXT                    | endereço de entrega                                     |
| valor_total | DECIMAL(10,2)           |                                                         |
| status      | ENUM                    | 'recebido','preparando','pronto','entregue','cancelado' |
| created_at  | DATETIME                |                                                         |

### Tabela `pedido_itens`

| Coluna     | Tipo                     | Obs                                 |
| ---------- | ------------------------ | ----------------------------------- |
| pedido_id  | INT FK→pedidos CASCADE   |                                     |
| produto_id | INT FK→produtos RESTRICT |                                     |
| quantidade | INT                      |                                     |
| preco      | DECIMAL(10,2)            | snapshot do preço na hora da compra |
| PK         | (pedido_id, produto_id)  |                                     |

### Tabela `notificacoes`

| Coluna     | Tipo                    | Obs                                                    |
| ---------- | ----------------------- | ------------------------------------------------------ |
| id         | INT PK AI               |                                                        |
| usuario_id | INT FK→usuarios CASCADE |                                                        |
| tipo       | VARCHAR(50)             | ex: 'agendamento', 'confirmacao', 'status', 'exclusao' |
| titulo     | VARCHAR(255)            |                                                        |
| mensagem   | TEXT                    |                                                        |
| lida       | TINYINT(1)              | 0 = não lida                                           |
| created_at | DATETIME                |                                                        |

### Tabela `logs`

| Coluna     | Tipo                    | Obs                                          |
| ---------- | ----------------------- | -------------------------------------------- |
| id         | INT PK AI               |                                              |
| usuario_id | INT FK→usuarios CASCADE |                                              |
| acao       | VARCHAR(100)            | ex: 'agendamento_criado', 'pedido_cancelado' |
| descricao  | TEXT                    | texto descritivo                             |
| created_at | DATETIME                |                                              |

### Tabela `site_config`

| Coluna | Tipo            | Obs                                  |
| ------ | --------------- | ------------------------------------ |
| chave  | VARCHAR(100) PK | ex: 'hero_titulo', 'footer_endereco' |
| valor  | TEXT            | conteúdo editável                    |
| titulo | VARCHAR(150)    | label para o painel admin            |
| grupo  | VARCHAR(50)     | 'landing' ou 'footer'                |

**Chaves padrão inseridas:**

- Landing: `hero_eyebrow`, `hero_titulo`, `hero_descricao`
- Footer: `footer_descricao`, `footer_endereco`, `footer_horario_1/2/3`, `footer_telefone`, `footer_whatsapp`, `footer_instagram`, `footer_copyright`

### Tabela `horarios_funcionamento`

| Coluna     | Tipo           | Obs                      |
| ---------- | -------------- | ------------------------ |
| id         | INT PK AI      |                          |
| dia_semana | TINYINT UNIQUE | 1=Seg … 7=Dom (ISO-8601) |
| abertura   | TIME           |                          |
| fechamento | TIME           |                          |
| fechado    | TINYINT(1)     | 0=aberto, 1=fechado      |

**Valores padrão:** Seg–Sex 08:30–19:00 (aberto), Sáb 08:00–12:00 (aberto), Dom (fechado).

---

## 7. Autenticação e Autorização

### Sessão PHP

```php
$_SESSION['usuario_id']    // int: ID do usuário logado
$_SESSION['usuario_nome']  // string: primeiro nome
$_SESSION['usuario_admin'] // bool/int: 1 se for admin
```

### Arquivos Guard

| Arquivo                       | Onde usar                        | Comportamento                                                                    |
| ----------------------------- | -------------------------------- | -------------------------------------------------------------------------------- |
| `api/auth/session.php`        | Views de cliente e admin         | redireciona para `view/login.php` se não logado                                  |
| `api/auth/require_admin.php`  | Views administrativas            | redireciona para `login.php` (sem sessão) ou `user/agendamentos.php` (sem admin) |
| `api/admin/session_admin.php` | Todos os endpoints `api/admin/*` | mesmo comportamento de require_admin.php                                         |

### Guards em endpoints de API

```php
helpers::verificar_login();  // retorna JSON 401 se não logado
helpers::verificar_admin();  // retorna JSON 401/403 se não logado ou não admin
```

### Login

- Usa `password_verify()` contra hash `password_hash()` no banco
- Após login: sessão iniciada + redirect por `helpers::resposta_json` com campo `data.redirect`
- Admin → `admin/dashboard.php`, Cliente → `user/agendamentos.php`

---

## 8. Padrão de Resposta da API (JSON)

**Todos os endpoints** retornam o mesmo envelope JSON:

```json
{
  "success": true | false,
  "message": "Texto para exibir ao usuário",
  "data": null | objeto | array
}
```

Gerado por: `helpers::resposta_json($sucesso, $mensagem, $dados, $httpStatusCode)`  
O método limpa todos os output buffers antes de responder e chama `exit`.

---

## 9. Endpoints da API

### `api/auth/login.php`

- **POST** — `action=login` → `{ email, senha }` → inicia sessão, retorna redirect

### `api/auth/cadastro.php`

- **POST** — `action=cadastro` → `{ nome, sobrenome, email, telefone, senha, cidade }` → cria usuário, inicia sessão

### `api/auth/logout.php`

- **GET/POST** → destrói sessão, redireciona para login

### `api/admin/agendamentos.php`

- **GET** → lista todos os agendamentos (limit 200)
- **POST** `action=criar` → `{ usuario_id, servico_id, data, hora_inicio, servicos_ids[], observacoes }`
- **POST** `action=editar` → `{ id, ...mesmos campos }`
- **POST** `action=status` → `{ id, status }` → altera status + notifica cliente
- **POST** `action=excluir` → `{ id }` → exclui + notifica cliente

### `api/admin/clientes.php`

- Gestão de clientes (listagem, edição, desativação)

### `api/admin/dashboard.php`

- **GET** → `{ stats: { total_clientes, agendamentos_hoje, receita_mes, novos_mes }, proximos: [...] }`

### `api/admin/horarios.php`

- Leitura e edição dos horários de funcionamento por dia da semana

### `api/admin/pedidos.php`

- Listagem e atualização de status dos pedidos

### `api/admin/produtos.php`

- **GET** → lista produtos com tags (GROUP_CONCAT)
- **POST** `action=criar` → `{ nome, descricao, preco, estoque, foto_url, tags: [id,...] }`
- **POST** `action=editar` → `{ id, ...campos }`
- **POST** `action=excluir` → `{ id }`

### `api/admin/servicos.php`

- **GET** → lista serviços
- **POST** `action=criar/editar/excluir`

### `api/admin/site-config.php`

- **GET** → todos os itens agrupados por grupo (`{ landing: [...], footer: [...] }`)
- **POST** → `{ chave, valor }` → salva conteúdo editável

### `api/admin/tags.php`

- CRUD de tags

### `api/admin/whatsapp.php`

- **POST** `action=individual` → `{ usuario_id, mensagem }` → envia mensagem a um cliente
- **POST** `action=enviar_dia` → `{ data }` → envia lembrete para todos agendados nessa data
- **POST** `action=promocional` → envio em massa para seleção de clientes
- Usa cURL para `https://dev-api.r4dev.com.br/v1/instance/.../messages/chat`

### `api/produtos/upload-foto.php`

- **POST** multipart/form-data, campo `foto`
- Aceita: JPEG, PNG, WebP, GIF — máximo 2 MB
- Valida MIME real com `finfo` (não só extensão)
- Salva em `assets/img/produtos/produto_{16hexchars}.ext`
- Retorna: `{ success: true, url: "assets/img/produtos/..." }`

### `api/user/agendamentos.php`

- **GET** `action=listar` → agendamentos do próprio usuário
- **GET** `action=agenda` → `{ inicio, fim }` → agenda disponível com horários
- **GET** `action=detalhar` → `{ id }` → detalhes de um agendamento
- **POST** `action=criar` → cria agendamento com `status=pendente` forçado
- **POST** `action=editar` → edita (não pode mudar status/usuario_id)
- **POST** `action=cancelar` → cancela apenas agendamento próprio
- **POST** `action=excluir` → exclui apenas agendamento próprio

### `api/user/carrinho.php`

- **GET** → `{ itens, total, count }`
- **POST** `action=adicionar` → `{ produto_id }` → valida estoque
- **POST** `action=atualizar` → `{ item_id, quantidade }` → 0 = remove
- **POST** `action=remover` → `{ item_id }`
- **POST** `action=limpar`

### `api/user/pedidos.php`

- **GET** → lista pedidos do usuário
- **POST** `action=finalizar` → `{ endereco }` → cria pedido com snapshot de preços, limpa carrinho

### `api/user/notificacoes.php`

- Listagem + contagem não lidas + marcar lida/todas lidas

### `api/user/perfil.php`

- Edição dos dados do perfil (nome, sobrenome, cidade, telefone, senha)

---

## 10. Camada SQL — DAOs

Todos os DAOs ficam em `sql/` e usam `Connection::getConnection()` internamente.

### `AgendamentosSql`

- `listarTodos(limite, offset)` — todos os agendamentos com JOINs completos
- `listarPorPeriodo(dataInicio, dataFim)` — para a agenda semanal
- `listarPorUsuario(usuarioId)` — agendamentos do cliente
- `listarPorUsuarioPaginado(usuarioId, limite, offset)`
- `contarPorUsuario(usuarioId)`
- `buscarPorId(id)` — retorna agendamento com dados completos
- `listarServicosDoAgendamento(agendamentoId)` — lista multi-serviço
- `salvarComServicos(body, id=null, usuarioId=null)` — insert ou update + sync `agendamento_servicos`
- `editarStatus(id, status)` — só altera o status
- `excluir(id)` — delete
- `excluirPorUsuario(id, usuarioId)` — delete com verificação de dono
- `cancelarPorUsuario(id, usuarioId)` — só cancela (não exclui)
- `listarAgendaPrivada(inicio, fim, usuarioId)` — agenda com slots livres/ocupados
- `estatisticas()` — contagens para o dashboard
- `proximosAgendamentos(limite)` — próximos agendamentos futuros
- **Colunas calculadas no SQL:** `hora_fim` (ADDTIME), `cliente` (CONCAT), `servico` (GROUP_CONCAT de nomes), `preco_servico` (SUM), `duracao_minutos` (SUM), `data_fmt` (DATE_FORMAT)

### `CarrinhoSql`

- `buscarOuCriar(usuarioId)` — retorna id do carrinho, criando se não existir
- `adicionarItem(carrinhoId, produtoId)` — valida estoque, incrementa ou insere
- `atualizarQtd(itemId, carrinhoId, quantidade)` — 0 remove o item
- `removerItem(itemId, carrinhoId)`
- `limpar(carrinhoId)`
- `listarItens(carrinhoId)` — com nome, preco, foto_url, subtotal calculado
- `contarItens(carrinhoId)` — total de itens (soma quantidades)

### `ClientesSql`

- `listar(limite, offset)` — apenas usuários com admin=0
- `buscarPorId(id)`, `editar(id, dados)`, `contar()`

### `DashboardSql`

- `buscarUsuario(id)` — dados do usuário logado
- `contarUsuarios()` — total de clientes (admin=0)
- `listarUsuarios(limite, offset)`
- `estatisticas()` — retorna `{ total_clientes, agendamentos_hoje, receita_mes, novos_mes }`

### `HorariosSql`

- `buscarTodos()` — mapa [dia_semana => { id, abertura, fechamento, fechado }]
- `buscarDia(diaSemana)` — com cache de request; fallback se não existir
- `nomesDias()` — mapa [1 => 'Segunda-feira', ..., 7 => 'Domingo']
- Usa `$cache` estático para evitar queries repetidas
- `dia_semana` segue ISO-8601 (1=Seg, 7=Dom), compatível com `date('N')` do PHP

### `LogsSql`

- `registrar(usuarioId, acao, descricao)` — insere log
- `listar(limite)` — últimos N logs

### `NotificacoesSql`

- `listarPorUsuario(usuarioId, limite)`
- `contarNaoLidas(usuarioId)`
- `criar(usuarioId, tipo, titulo, mensagem)`
- `marcarComoLida(id, usuarioId)`
- `marcarTodasComoLidas(usuarioId)`

### `PedidosSql`

- `criar(usuarioId, endereco, itens)` — snapshot de preços, decrementa estoque
- `listarPorUsuario(usuarioId)` — com itens do pedido
- `listarTodos(limite, offset)` — admin
- `atualizarStatus(id, status)`
- `buscarPorId(id)`

### `ProdutosSql`

- `listarProdutos(pdo)` — com GROUP_CONCAT de tags e tag_ids
- `adicionarProdutos(pdo, dados)` — suporte a array ou $\_POST
- `adicionarTagProduto(pdo, idProduto, idTag)`
- `listarTags(pdo)`
- `excluirProduto(pdo, idProduto)` — limpa produto_tags depois deleta produto
- `editarProduto(pdo, id, dados)` — update + sync tags

### `ServicosSql`

- `listar()`, `buscarPorId(id)`, `criar(dados)`, `editar(id, dados)`, `excluir(id)`, `contar()`

### `SiteConfigSql`

- `buscarTodos()` — agrupado por grupo
- `buscarGrupo(grupo)` — mapa chave→valor de um grupo
- `get(chave, fallback)` — com cache de request
- `salvar(chave, valor)` — UPDATE (lança RuntimeException se chave não existir)

### `UsuariosSql`

- Gerenciamento de usuários (cadastro, busca, atualização de perfil)

---

## 11. Helpers (`helpers/helpers.php`)

Classe estática `helpers`:

```php
helpers::resposta_json(bool $sucesso, string $mensagem, mixed $dados, int $status)
// → limpa ob, seta http_response_code, header JSON, echo json_encode, exit

helpers::verificar_login()
// → retorna JSON 401 se $_SESSION['usuario_id'] vazio

helpers::verificar_admin()
// → verifica_login() + retorna JSON 403 se não admin

helpers::validarTexto($texto, $nomeCampo, $min=2, $max=50)
// → trim, colapsa espaços, valida comprimento e regex unicode [letra+espaço]
// → retorna texto limpo ou resposta_json 400

helpers::validarEmail($email)
// → filter_var FILTER_VALIDATE_EMAIL, retorna strtolower ou 400

helpers::validarTelefone($telefone)
// → regex: /^\+55 \(\d{2}\) \d{5}-\d{4}$/
// → retorna apenas dígitos ou 400

helpers::normalizarTelefone($telefone)
// → remove não-dígitos, adiciona '55' se necessário
// → valida: /^55\d{10,11}$/ ou 400

helpers::validarSenha($senha)
// → mínimo 8 chars, sem espaços → retorna senha ou 400
```

---

## 12. Controllers

Os controllers ficam em `controllers/` e são incluídos pelas views antes da renderização HTML.

**Padrão:** Incluem o guard de sessão + carregam dados via DAOs → expõem variáveis PHP para a view.

### `controllers/agendamentos.controller.php`

Expõe: `$agendamentos`, `$totalAgendamentos`, `$stats`, `$servicos`, `$clientes`, `$weekDates`, `$agendaMap`  
Também calcula a semana atual e mapeia os agendamentos por `[data][hora_inicio]`.

### `controllers/clientes.controller.php`

Expõe dados de listagem de clientes com paginação.

### `controllers/dashboard.controller.php`

Expõe: `$stats`, `$proximos`, `$logs` (últimos logs do sistema).

### `controllers/produtos.controller.php`

Expõe: `$produtos`, `$tags`.

### `controllers/servicos.controller.php`

Expõe: `$servicos`.

---

## 13. Views

### Área Pública

- `view/index.php` — landing page; lê conteúdo de `site_config` (grupo 'landing' e 'footer')
- `view/login.php` / `view/cadastro.php` — formulários
- `view/catalogo.php` — catálogo de produtos, acessível sem login

### Área Admin (`view/admin/`)

Cada view:

1. Inclui `require_admin.php`
2. Define `$activePage` e `$pageTitle`
3. Inclui o controller correspondente
4. Inclui `head.php`, `sidebar.php`, `topbar.php`
5. Renderiza HTML com dados PHP embutidos
6. O JS da página consome a API correspondente via `fetch()`

Menu lateral (sidebar.php) links: Dashboard, Clientes, Agendamentos, Serviços, Produtos, Editor do Site, Horários + Ver Site + Logout

### Área do Cliente (`view/user/`)

- Inclui `api/auth/session.php` como guard
- Mesma estrutura de head/sidebar adaptada para o cliente

---

## 14. Frontend

### CSS

- **Admin:** `assets/css/admin/app.css` (importa base.css, layout.css, pages.css)
- **Público:** `assets/css/public/app.css` (importa tokens.css e demais módulos)
- **Compartilhado:** `assets/css/shared/` (agenda.css, components.css)
- **Auth:** `assets/css/auth/` (login.css, cadastro.css)
- Usa variáveis CSS (tokens) definidas em `tokens.css`

### JavaScript

Todos os arquivos JS em `assets/js/admin/` são carregados pelas views admin correspondentes e usam `fetch()` para chamar os endpoints de API.

Padrão JS nas páginas admin:

```js
// Ao carregar a página
carregarDados(); // fetch GET → popula tabela
// Em formulários
salvar({ action: "criar" | "editar", ...dados }); // fetch POST
excluir(id); // fetch POST action=excluir
// Resposta sempre: { success, message, data }
// Sucesso → SweetAlert2 toast + reload/atualização
// Erro → SweetAlert2 alert com message
```

### SweetAlert2

- Instalado via npm em `node_modules/sweetalert2/`
- CSS: `node_modules/sweetalert2/dist/sweetalert2.min.css`
- JS: referenciado nas views via path relativo

### Font Awesome 6.5

- Carregado via CDN em `head.php` e `head_public.php`
- Usado extensivamente nos ícones de botões, menu, cards

---

## 15. Regras de Negócio Críticas

### Agendamentos

- Um agendamento pode ter **múltiplos serviços** (`agendamento_servicos`). O `servico_id` na tabela principal é o serviço "principal" (legado/fallback).
- `hora_fim` é **calculada no SQL** com `ADDTIME(hora_inicio, SEC_TO_TIME(SUM(tempo_estimado) * 60))`.
- O sistema bloqueia horários já ocupados somando durações dos serviços.
- Clientes criam agendamentos com `status='pendente'` forçado (não podem definir o status).
- Admins podem criar, editar, alterar status e excluir qualquer agendamento.
- Ao confirmar → notificação criada para o cliente.

### Carrinho e Pedidos

- 1 carrinho por usuário (UNIQUE em `carrinho.usuario_id`).
- `adicionarItem` valida estoque antes de incrementar.
- Ao finalizar pedido: preço é copiado como snapshot (`pedido_itens.preco`), carrinho é limpo.
- `PedidosSql::criar` decrementa o estoque dos produtos.
- Não há gateway de pagamento — pedido começa com `status='recebido'`.

### Upload de Imagem

- Apenas para produtos. Endpoint: `api/produtos/upload-foto.php`.
- Valida MIME real (finfo), não apenas extensão.
- Nome gerado: `produto_{16 bytes hex}.ext` (via `bin2hex(random_bytes(8))`).
- Limite: 2 MB.

### Logs

- Registrados em toda ação significativa (admin e cliente).
- Ações comuns: `agendamento_criado`, `agendamento_editado`, `agendamento_cancelado`, `agendamento_excluido`, `pedido_criado`, `login`.
- `LogsSql::registrar(usuarioId, acao, descricao)`.

### Notificações

- Criadas automaticamente pelo backend ao ocorrer eventos (agendamento criado, status alterado, excluído, confirmado).
- `lida=0` por padrão; cliente marca como lida via API.

### Editor do Site

- Textos da landing page e do footer são armazenados em `site_config`.
- Admin edita via `view/admin/site-editor.php` → salva via `api/admin/site-config.php`.
- A landing page (`view/index.php`) carrega com `SiteConfigSql::buscarGrupo('landing')` e usa fallbacks hardcoded.

### WhatsApp

- Endpoint: `https://dev-api.r4dev.com.br/v1/instance/cmqqzc2j1002d104shfslo3sj/messages/chat`
- Token: `cmqqzc2j2002e104so1o09hqy` (hardcoded em `api/admin/whatsapp.php`)
- Telefone normalizado para `55{DDD}{numero}` antes do envio (sem formatação).
- Timeout cURL: 15 segundos. SSL verificado.

---

## 16. Segurança

- **Senhas:** `password_hash()` / `password_verify()` (bcrypt)
- **SQL Injection:** todos os DAOs usam PDO com prepared statements e `bindValue`/`execute(array)`
- **Autorização:** guards PHP checam sessão antes de qualquer lógica
- **Upload:** validação de MIME real com `finfo`, whitelist de tipos, nome aleatório
- **XSS:** views PHP usam `htmlspecialchars()` para exibir dados do banco
- **Acesso a páginas admin:** `require_admin.php` redireciona se não logado ou sem permissão
- **Validação de input:** `helpers::validar*` em cadastro e perfil

---

## 17. Convenções do Time

### PHP

- Arquivos de API retornam **sempre JSON** via `helpers::resposta_json`
- `try/catch` em todos os endpoints: `InvalidArgumentException` → 422, `RuntimeException` → 409, `Throwable` → 500
- DAOs são estáticos (exceto `ProdutosSql` e `CarrinhoSql` que recebem `$pdo` como parâmetro — padrão legado, os mais novos chamam `Connection::getConnection()` internamente)
- `include_once` para configs/helpers; `require_once` para guards e dependências críticas
- Variáveis de sessão só são lidas/escritas nos arquivos de `api/auth/` e guards

### Nomenclatura de Arquivos

- Views admin: `view/admin/{recurso}.php`
- APIs admin: `api/admin/{recurso}.php`
- APIs user: `api/user/{recurso}.php`
- DAOs: `sql/{Entidade}Sql.php`
- Controllers: `controllers/{recurso}.controller.php`
- JS admin: `assets/js/admin/{recurso}.js`

### HTTP Actions (POST body)

- Todas as ações POST passam o campo `action` no body JSON: `'criar'`, `'editar'`, `'excluir'`, `'status'`
- Corpo lido com `json_decode(file_get_contents('php://input'), true) ?? []`

### CSS

- Admin usa design system próprio com variáveis em `base.css`
- Público usa `tokens.css` como fonte de variáveis
- Responsividade em `responsive.css`

---

## 18. Ambiente de Desenvolvimento

```
Servidor:  XAMPP (Windows)
PHP:       versão local (XAMPP bundle)
MySQL:     porta 3307 (não 3306 — configuração não-padrão)
Database:  tiopretobarbearia
User:      root
Senha:     (vazia)
Caminho:   C:/xampp2/htdocs/tiopretobarbearia-crud/
URL local: http://localhost/tiopretobarbearia-crud/
```

Para criar o banco do zero:

```
mysql -h 127.0.0.1 -P 3307 -u root tiopretobarbearia < sql/migrations/000_schema_completo.sql
```

---

## 19. Mapa Rápido de Dependências

```
view/admin/*.php
  └─ api/auth/require_admin.php
  └─ controllers/*.controller.php
       └─ api/auth/session.php
       └─ sql/*Sql.php
            └─ config/Connection.php
  └─ view/partials/head.php  (CSS admin + SweetAlert2)
  └─ view/partials/sidebar.php
  └─ view/partials/topbar.php
  └─ assets/js/admin/{recurso}.js
       └─ fetch → api/admin/{recurso}.php
                    └─ api/admin/session_admin.php
                    └─ sql/*Sql.php
                    └─ helpers/helpers.php
```

---

_Gerado automaticamente em 2026-07-13 — Time PDA._
