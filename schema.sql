-- Crie o banco antes de executar este arquivo: CREATE DATABASE saep_db;

CREATE TABLE IF NOT EXISTS usuarios (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(128) NOT NULL,
    email VARCHAR(128) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    perfil VARCHAR(32) NOT NULL CHECK (perfil IN ('admin', 'veterinario', 'recepcionista')),
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tutores (
    id SERIAL PRIMARY KEY,
    nome VARCHAR(128) NOT NULL,
    cpf_criptografado VARCHAR(255) NOT NULL,
    telefone VARCHAR(32) NOT NULL,
    email VARCHAR(128) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS pets (
    id SERIAL PRIMARY KEY,
    tutor_id INTEGER NOT NULL REFERENCES tutores(id),
    nome VARCHAR(128) NOT NULL,
    especie VARCHAR(64) NOT NULL,
    raca VARCHAR(128) NOT NULL,
    data_nascimento DATE NOT NULL,
    cadastrado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS agendamentos (
    id SERIAL PRIMARY KEY,
    tutor_id INTEGER NOT NULL REFERENCES tutores(id),
    pet_id INTEGER NOT NULL REFERENCES pets(id),
    usuario_id INTEGER NOT NULL REFERENCES usuarios(id),
    data_agendamento DATE NOT NULL,
    hora_agendamento TIME NOT NULL,
    servico VARCHAR(128) NOT NULL,
    status VARCHAR(16) NOT NULL DEFAULT 'AGENDADO'
        CHECK (status IN ('AGENDADO', 'CONCLUIDO', 'CANCELADO')),
    observacao TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Usuários de demonstração. Senha para todos: 123456.
INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES
('Dra. Camila Alencar', 'camila.vet@vetsystem.com', '$argon2id$v=19$m=65536,t=4,p=1$SEh1VEZMZFZvVW9sN3l1eg$Hnc2Gno/SSwCVmmNNxMk4+RqrHmZgcK4Sbw5ZIynpVY', 'veterinario'),
('Lucas Fernandes', 'lucas.recepcao@vetsystem.com', '$argon2id$v=19$m=65536,t=4,p=1$SEh1VEZMZFZvVW9sN3l1eg$Hnc2Gno/SSwCVmmNNxMk4+RqrHmZgcK4Sbw5ZIynpVY', 'recepcionista'),
('Mariana Rocha', 'gestao@vetsystem.com', '$argon2id$v=19$m=65536,t=4,p=1$SEh1VEZMZFZvVW9sN3l1eg$Hnc2Gno/SSwCVmmNNxMk4+RqrHmZgcK4Sbw5ZIynpVY', 'admin')
ON CONFLICT (email) DO NOTHING;

-- CPFs fictícios para demonstração, cifrados em Base64 com OpenSSL.
INSERT INTO tutores (id, nome, cpf_criptografado, telefone, email) VALUES
(1, 'Carlos Eduardo Silva', 'yjSV6v869dRV2Ovmwagjfm3WwQSIzTIfmDHRM2yaPF4=', '(11) 98765-4321', 'carlos.silva@email.com'),
(2, 'Beatriz Souza Martins', 'JAoIrUeF/ADiRnjZmXAztXqEOw7TNOCmjbB6ZVLNyC0=', '(11) 97654-3210', 'beatriz.martins@email.com'),
(3, 'Fernando Henrique Lima', 'mC9XW6mSindmEBCFwiiVU5pQ1me7UzpPI91NwpiHQ8c=', '(19) 99123-4567', 'fernando.lima@email.com')
ON CONFLICT (id) DO NOTHING;

INSERT INTO pets (id, tutor_id, nome, especie, raca, data_nascimento) VALUES
(1, 1, 'Thor', 'Canina', 'Golden Retriever', '2021-05-10'),
(2, 2, 'Mia', 'Felina', 'Siamês', '2022-08-15'),
(3, 3, 'Bob', 'Canina', 'Bulldog Francês', '2020-02-20')
ON CONFLICT (id) DO NOTHING;

INSERT INTO agendamentos (id, tutor_id, pet_id, usuario_id, data_agendamento, hora_agendamento, servico, status, observacao) VALUES
(1, 1, 1, 1, '2024-11-15', '09:30:00', 'Consulta Geral e Vacinação Anual', 'CONCLUIDO', 'Animal dócil e com carteira atualizada'),
(2, 2, 2, 1, '2025-06-20', '14:00:00', 'Profilaxia Dentária e Tartarectomia', 'CONCLUIDO', 'Exames pré-operatórios normais'),
(3, 3, 3, 2, '2026-03-10', '10:30:00', 'Check-up Preventivo e Exame de Sangue', 'AGENDADO', 'Paciente alérgico a dipirona')
ON CONFLICT (id) DO NOTHING;

SELECT setval('usuarios_id_seq', (SELECT MAX(id) FROM usuarios));
SELECT setval('tutores_id_seq', (SELECT MAX(id) FROM tutores));
SELECT setval('pets_id_seq', (SELECT MAX(id) FROM pets));
SELECT setval('agendamentos_id_seq', (SELECT MAX(id) FROM agendamentos));
