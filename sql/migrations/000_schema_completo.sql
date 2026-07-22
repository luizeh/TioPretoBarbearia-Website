-- ============================================================
-- Schema completo — tiopretobarbearia
-- Gerado em: 2026-07-10
--
-- Este script é AGNÓSTICO DE BANCO: não contém USE nem CREATE DATABASE,
-- então as tabelas são criadas no banco já selecionado pela conexão.
-- O nome do banco fica a cargo do ambiente (ver DB_NAME em config/Connection.php).
--
-- ▸ Local (XAMPP, porta 3307 — banco "tiopretobarbearia"):
--     mysql -h 127.0.0.1 -P 3307 -u root tiopretobarbearia < 000_schema_completo.sql
--
-- ▸ Railway (plugin MySQL — o banco padrão se chama "railway"):
--   1) No serviço MySQL do Railway, copie as credenciais da aba "Variables".
--   2) Importe apontando para o banco "railway" (NÃO "tiopretobarbearia",
--      que não existe lá). Duas opções:
--        a) via CLI, usando a connection string pública do Railway:
--             mysql --protocol=TCP -h <MYSQLHOST> -P <MYSQLPORT> \
--                   -u <MYSQLUSER> -p<MYSQLPASSWORD> railway < 000_schema_completo.sql
--        b) ou cole o conteúdo deste arquivo na aba "Data"/Query do serviço.
--   3) No serviço da APP, defina as variáveis de ambiente:
--        DB_HOST=${{MySQL.MYSQLHOST}}   DB_PORT=${{MySQL.MYSQLPORT}}
--        DB_NAME=${{MySQL.MYSQLDATABASE}}  (= "railway")
--        DB_USER=${{MySQL.MYSQLUSER}}   DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
--      (a sintaxe ${{Serviço.VAR}} é a referência de variáveis do Railway)
--
-- Todas as tabelas usam IF NOT EXISTS — seguro de re-executar.
-- Ordem respeitando dependências de FK.
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- 1. usuarios
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id         INT           NOT NULL AUTO_INCREMENT,
    nome       VARCHAR(100)  NOT NULL,
    sobrenome  VARCHAR(100)  NOT NULL,
    email      VARCHAR(320)  NOT NULL,
    telefone   VARCHAR(20)   NOT NULL,
    senha      VARCHAR(255)  NOT NULL,
    cidade     VARCHAR(255)  NOT NULL,
    admin      TINYINT(1)    NOT NULL DEFAULT 0,
    promovido_por INT        NULL COMMENT 'id do admin que promoveu este usuário a admin (NULL = admin original)',
    email_verificado       TINYINT(1)   NOT NULL DEFAULT 0,
    email_verificado_em    DATETIME     NULL,
    telefone_verificado    TINYINT(1)   NOT NULL DEFAULT 0,
    telefone_verificado_em DATETIME     NULL,
    email_pendente         VARCHAR(320) NULL COMMENT 'novo e-mail aguardando verificação (troca)',
    telefone_pendente      VARCHAR(20)  NULL COMMENT 'novo telefone aguardando verificação (troca)',
    created_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP     NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_usuarios_email    (email),
    UNIQUE KEY uq_usuarios_telefone (telefone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 1b. codigos_verificacao  (estrutura reutilizável de códigos)
--     Usada para verificação de e-mail, de telefone (WhatsApp) e
--     para recuperação de senha.
--       proposito: 'email_verificacao' | 'telefone_verificacao' | 'recuperacao'
--       canal:     'email' | 'whatsapp'
--       destino:   e-mail ou telefone (só dígitos) para onde o código foi enviado
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS codigos_verificacao (
    id          INT          NOT NULL AUTO_INCREMENT,
    usuario_id  INT          NOT NULL,
    proposito   VARCHAR(30)  NOT NULL DEFAULT 'email_verificacao',
    canal       VARCHAR(15)  NOT NULL DEFAULT 'email',
    destino     VARCHAR(320) NULL COMMENT 'e-mail ou telefone de destino do código',
    email       VARCHAR(320) NULL COMMENT 'legado — mantido por compatibilidade',
    codigo_hash VARCHAR(255) NOT NULL COMMENT 'hash do código — nunca em texto puro',
    expira_em   DATETIME     NOT NULL,
    tentativas  TINYINT      NOT NULL DEFAULT 0,
    usado       TINYINT(1)   NOT NULL DEFAULT 0,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    INDEX idx_cv_usuario (usuario_id),
    INDEX idx_cv_usuario_proposito (usuario_id, proposito, usado),

    CONSTRAINT fk_cv_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. servicos
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS servicos (
    id             INT            NOT NULL AUTO_INCREMENT,
    nome           VARCHAR(150)   NOT NULL,
    descricao      TEXT           NULL,
    preco          DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    tempo_estimado INT            NOT NULL DEFAULT 30 COMMENT 'duração em minutos',
    foto_url       VARCHAR(500)   NULL,
    ativo          TINYINT(1)     NOT NULL DEFAULT 1 COMMENT '1 = ativo; 0 = excluído logicamente (mantido só p/ histórico)',
    created_at     TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at     TIMESTAMP      NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. agendamentos
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS agendamentos (
    id          INT   NOT NULL AUTO_INCREMENT,
    usuario_id  INT   NOT NULL,
    servico_id  INT   NOT NULL,
    data        DATE  NOT NULL,
    hora_inicio TIME  NOT NULL,
    hora_fim    TIME  NOT NULL,
    status      ENUM('pendente','confirmado','cancelado','finalizado') NOT NULL DEFAULT 'pendente',
    observacoes TEXT  NULL,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    INDEX idx_agendamentos_data_status (data, status),
    INDEX idx_agendamentos_usuario     (usuario_id),

    CONSTRAINT fk_agendamentos_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    CONSTRAINT fk_agendamentos_servico
        FOREIGN KEY (servico_id) REFERENCES servicos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. agendamento_servicos  (multi-serviço por agendamento)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS agendamento_servicos (
    agendamento_id INT NOT NULL,
    servico_id     INT NOT NULL,

    PRIMARY KEY (agendamento_id, servico_id),

    CONSTRAINT fk_agendamento_servicos_agendamento
        FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_agendamento_servicos_servico
        FOREIGN KEY (servico_id) REFERENCES servicos(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. tags
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS tags (
    id   INT          NOT NULL AUTO_INCREMENT,
    nome VARCHAR(80)  NOT NULL,

    PRIMARY KEY (id),
    UNIQUE KEY uq_tags_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 6. produtos
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS produtos (
    id        INT            NOT NULL AUTO_INCREMENT,
    nome      VARCHAR(150)   NOT NULL,
    descricao TEXT           NULL,
    preco     DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    estoque   INT            NOT NULL DEFAULT 0,
    foto_url  VARCHAR(500)   NULL,
    visivel   TINYINT(1)     NOT NULL DEFAULT 1 COMMENT '1 = visível no site; 0 = só admin',
    ativo     TINYINT(1)     NOT NULL DEFAULT 1 COMMENT '1 = ativo; 0 = excluído logicamente (mantido só p/ histórico)',
    created_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP     NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 7. produto_tags
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS produto_tags (
    produto_id INT NOT NULL,
    tag_id     INT NOT NULL,

    PRIMARY KEY (produto_id, tag_id),

    CONSTRAINT fk_produto_tags_produto
        FOREIGN KEY (produto_id) REFERENCES produtos(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_produto_tags_tag
        FOREIGN KEY (tag_id) REFERENCES tags(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 8. carrinho
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS carrinho (
    id         INT      NOT NULL AUTO_INCREMENT,
    usuario_id INT      NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_carrinho_usuario (usuario_id),

    CONSTRAINT fk_carrinho_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 9. carrinho_itens
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS carrinho_itens (
    id          INT NOT NULL AUTO_INCREMENT,
    carrinho_id INT NOT NULL,
    produto_id  INT NOT NULL,
    quantidade  INT NOT NULL DEFAULT 1,

    PRIMARY KEY (id),
    UNIQUE KEY uq_carrinho_produto (carrinho_id, produto_id),

    CONSTRAINT fk_carrinho_itens_carrinho
        FOREIGN KEY (carrinho_id) REFERENCES carrinho(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_carrinho_itens_produto
        FOREIGN KEY (produto_id) REFERENCES produtos(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 10. pedidos
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS pedidos (
    id          INT            NOT NULL AUTO_INCREMENT,
    usuario_id  INT            NOT NULL,
    endereco    TEXT           NOT NULL COMMENT 'endereço completo formatado (compatibilidade)',
    cep              VARCHAR(9)   NULL,
    logradouro       VARCHAR(150) NULL,
    numero           VARCHAR(20)  NULL,
    bairro           VARCHAR(100) NULL,
    cidade           VARCHAR(100) NULL,
    estado           CHAR(2)      NULL,
    complemento      VARCHAR(150) NULL,
    ponto_referencia VARCHAR(150) NULL,
    valor_total DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    status      ENUM('recebido','preparando','pronto','entregue','cancelado') NOT NULL DEFAULT 'recebido',
    created_at  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP      NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    INDEX idx_pedidos_usuario (usuario_id),

    CONSTRAINT fk_pedidos_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 11. pedido_itens
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS pedido_itens (
    id         INT            NOT NULL AUTO_INCREMENT,
    pedido_id  INT            NOT NULL,
    produto_id INT            NOT NULL,
    produto_nome VARCHAR(150) NULL COMMENT 'nome do produto no momento da compra (snapshot histórico)',
    quantidade INT            NOT NULL,
    preco      DECIMAL(10,2)  NOT NULL COMMENT 'preço snapshot no momento da compra',

    PRIMARY KEY (id),
    UNIQUE KEY uq_pedido_produto (pedido_id, produto_id),

    CONSTRAINT fk_pedido_itens_pedido
        FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_pedido_itens_produto
        FOREIGN KEY (produto_id) REFERENCES produtos(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 12. notificacoes
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS notificacoes (
    id         INT           NOT NULL AUTO_INCREMENT,
    usuario_id INT           NOT NULL,
    tipo       VARCHAR(50)   NOT NULL,
    titulo     VARCHAR(255)  NOT NULL,
    mensagem   TEXT          NOT NULL,
    lida       TINYINT(1)    NOT NULL DEFAULT 0,
    created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    INDEX idx_notificacoes_usuario_lida (usuario_id, lida),

    CONSTRAINT fk_notificacoes_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 13. logs
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS logs (
    id         INT           NOT NULL AUTO_INCREMENT,
    usuario_id INT           NOT NULL,
    acao       VARCHAR(100)  NOT NULL,
    descricao  TEXT          NOT NULL,
    created_at DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    INDEX idx_logs_usuario (usuario_id),

    CONSTRAINT fk_logs_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 14. site_config
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS site_config (
    chave  VARCHAR(100) NOT NULL,
    valor  TEXT         NOT NULL,
    titulo VARCHAR(150) NOT NULL COMMENT 'Label exibido no painel admin',
    grupo  VARCHAR(50)  NOT NULL COMMENT 'landing | footer',

    PRIMARY KEY (chave),
    INDEX idx_site_config_grupo (grupo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO site_config (chave, valor, titulo, grupo) VALUES
  ('hero_eyebrow',     '✦ Estilo & Tradição',    'Subtítulo hero',          'landing'),
  ('hero_titulo',      'Arte em cada corte.',     'Título principal',        'landing'),
  ('hero_descricao',   'Na Tio Preto Barbearia, cada detalhe é pensado para realçar o melhor de você — do corte à barba, com produtos premium e técnica impecável.', 'Descrição hero', 'landing'),
  ('footer_descricao', 'Tradição, estilo e cuidado em cada detalhe. A Tio Preto Barbearia é o seu espaço de confiança para sair sempre com o melhor visual.', 'Descrição do rodapé', 'footer'),
  ('footer_endereco',  'Rua Joao Vasques, 180 — Ana Laura 2, Douradina-PR', 'Endereço', 'footer'),
  ('footer_horario_1', 'Ter – Sex: 8h30 às 19h',  'Horário — linha 1',      'footer'),
  ('footer_horario_2', 'Sábado: 08h ao 12h',       'Horário — linha 2',      'footer'),
  ('footer_horario_3', 'Domingo e Segunda: Fechado','Horário — linha 3',     'footer'),
  ('footer_telefone',  '+5544998603404',           'Telefone (formato +55…)','footer'),
  ('footer_whatsapp',  '554498603404',             'WhatsApp (só números)',  'footer'),
  ('footer_instagram', 'tiopretobarbearia',         'Instagram (handle, sem @)', 'footer'),
  ('footer_copyright', '© 2026 Tio Preto Barbearia — Todos os direitos reservados.', 'Texto de copyright', 'footer');

-- --------------------------------------------------------
-- 15. horarios_funcionamento
--     Padrão por dia-da-semana (ISO-8601: 1=Seg … 7=Dom)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS horarios_funcionamento (
    id         INT         NOT NULL AUTO_INCREMENT,
    dia_semana TINYINT     NOT NULL COMMENT '1=Segunda … 7=Domingo (ISO-8601)',
    abertura   TIME        NOT NULL DEFAULT '08:00:00',
    fechamento TIME        NOT NULL DEFAULT '20:00:00',
    fechado    TINYINT(1)  NOT NULL DEFAULT 0,

    PRIMARY KEY (id),
    UNIQUE KEY uq_horarios_dia (dia_semana)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO horarios_funcionamento (dia_semana, abertura, fechamento, fechado) VALUES
  (1, '08:30:00', '19:00:00', 0),  -- Segunda-feira
  (2, '08:30:00', '19:00:00', 0),  -- Terça-feira
  (3, '08:30:00', '19:00:00', 0),  -- Quarta-feira
  (4, '08:30:00', '19:00:00', 0),  -- Quinta-feira
  (5, '08:30:00', '19:00:00', 0),  -- Sexta-feira
  (6, '08:00:00', '12:00:00', 0),  -- Sábado
  (7, '08:00:00', '12:00:00', 1);  -- Domingo (fechado)

-- --------------------------------------------------------
-- 16. horarios_excecoes
--     Exceções por data específica (sobrepõem o padrão semanal)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS horarios_excecoes (
    data       DATE        NOT NULL,
    fechado    TINYINT(1)  NOT NULL DEFAULT 0,
    abertura   TIME        NULL     COMMENT 'NULL = usa o padrão do dia da semana',
    fechamento TIME        NULL     COMMENT 'NULL = usa o padrão do dia da semana',

    PRIMARY KEY (data)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 17. horarios_bloqueios
--     Bloqueios recorrentes dentro do horário de funcionamento
--     (ex: almoço 12:00-13:00 todos os dias, ou apenas às quartas)
--     dia_semana NULL = aplica a todos os dias da semana
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS horarios_bloqueios (
    id           INT          NOT NULL AUTO_INCREMENT,
    dia_semana   TINYINT      NULL     COMMENT 'NULL = todo dia; 1=Seg … 7=Dom (ISO-8601)',
    dias_excecao VARCHAR(20)  NULL     COMMENT 'Dias ISO excluídos quando dia_semana IS NULL, ex: "6" ou "6,7"',
    hora_inicio  TIME         NOT NULL,
    hora_fim     TIME         NOT NULL,
    descricao    VARCHAR(100) NULL,

    PRIMARY KEY (id),
    INDEX idx_bloqueios_dia (dia_semana)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 18. horarios_periodos
--     Bloqueios por período (intervalo de datas) — ex.: férias.
--     hora_inicio/hora_fim NULL  = fecha o dia inteiro em todo o intervalo.
--     hora_inicio/hora_fim setados = fecha apenas essa faixa em cada dia do intervalo.
--     Prioridade de resolução por data:
--       horarios_excecoes (data específica) > horarios_periodos > horarios_funcionamento.
--     Assim uma exceção por data pode reabrir um dia dentro de um período fechado.
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS horarios_periodos (
    id          INT          NOT NULL AUTO_INCREMENT,
    data_inicio DATE         NOT NULL,
    data_fim    DATE         NOT NULL,
    hora_inicio TIME         NULL     COMMENT 'NULL = dia inteiro fechado; senão fecha só esta faixa',
    hora_fim    TIME         NULL,
    descricao   VARCHAR(150) NULL,

    PRIMARY KEY (id),
    INDEX idx_periodos_datas (data_inicio, data_fim)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 19. sessoes
--     Armazenamento das sessões PHP no banco (ver helpers/DbSessionHandler.php).
--     Evita que o login caia a cada deploy no Railway (o /tmp padrão é efêmero).
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS sessoes (
    id            VARCHAR(128) NOT NULL,
    payload       MEDIUMTEXT   NOT NULL,
    ultimo_acesso INT          NOT NULL,

    PRIMARY KEY (id),
    INDEX idx_sessoes_acesso (ultimo_acesso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;


