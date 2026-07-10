ALTER TABLE agendamentos
    ADD COLUMN observacoes TEXT NULL AFTER status;

CREATE TABLE agendamento_servicos (
    agendamento_id INT NOT NULL,
    servico_id INT NOT NULL,
    PRIMARY KEY (agendamento_id, servico_id),
    CONSTRAINT fk_agendamento_servicos_agendamento
        FOREIGN KEY (agendamento_id) REFERENCES agendamentos(id) ON DELETE CASCADE,
    CONSTRAINT fk_agendamento_servicos_servico
        FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
