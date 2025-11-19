-- Script de Criação do Banco de Dados Vigged
-- Plataforma de Inclusão e Oportunidades
-- Execute este script para criar o banco de dados e todas as tabelas

-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS vigged_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE vigged_db;

-- Tabela: users (Candidatos PCD e Administradores)
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('pcd', 'admin') DEFAULT 'pcd',
    cpf VARCHAR(14) UNIQUE,
    telefone VARCHAR(20),
    data_nascimento DATE,
    
    -- Informações sobre deficiência
    tipo_deficiencia ENUM('fisica', 'visual', 'auditiva', 'intelectual', 'multipla', 'tea', 'outra'),
    especifique_outra TEXT,
    cid VARCHAR(10),
    possui_laudo BOOLEAN DEFAULT FALSE,
    laudo_medico_path VARCHAR(500),
    
    -- Recursos de acessibilidade
    recursos_acessibilidade JSON,
    outras_necessidades TEXT,
    
    -- Status e controle
    status ENUM('ativo', 'inativo', 'pendente') DEFAULT 'pendente',
    email_verificado BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_tipo (tipo),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: companies (Empresas)
CREATE TABLE IF NOT EXISTS companies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    razao_social VARCHAR(255) NOT NULL,
    nome_fantasia VARCHAR(255),
    cnpj VARCHAR(18) UNIQUE NOT NULL,
    data_fundacao DATE,
    porte_empresa ENUM('mei', 'micro', 'pequena', 'media', 'grande'),
    setor VARCHAR(100),
    website VARCHAR(255),
    descricao TEXT,
    
    -- Endereço
    cep VARCHAR(9),
    estado VARCHAR(2),
    cidade VARCHAR(100),
    bairro VARCHAR(100),
    logradouro VARCHAR(255),
    numero VARCHAR(20),
    complemento VARCHAR(100),
    
    -- Contato
    email_corporativo VARCHAR(255) UNIQUE NOT NULL,
    telefone_empresa VARCHAR(20),
    
    -- Responsável
    nome_responsavel VARCHAR(255),
    cargo_responsavel VARCHAR(100),
    email_responsavel VARCHAR(255),
    telefone_responsavel VARCHAR(20),
    
    -- Compromisso com inclusão
    ja_contrata_pcd BOOLEAN DEFAULT FALSE,
    recursos_acessibilidade JSON,
    politica_inclusao TEXT,
    
    -- Documentação
    documento_empresa_path VARCHAR(500),
    logo_path VARCHAR(500),
    
    -- Autenticação
    senha VARCHAR(255) NOT NULL,
    
    -- Status e controle
    status ENUM('ativa', 'inativa', 'pendente') DEFAULT 'pendente',
    plano ENUM('gratuito', 'essencial', 'profissional', 'enterprise') DEFAULT 'gratuito',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_cnpj (cnpj),
    INDEX idx_email (email_corporativo),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: jobs (Vagas)
CREATE TABLE IF NOT EXISTS jobs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    requisitos TEXT,
    localizacao VARCHAR(255),
    tipo_contrato ENUM('CLT', 'PJ', 'Estagio', 'Temporario'),
    faixa_salarial VARCHAR(100),
    
    -- Status e controle
    status ENUM('ativa', 'pausada', 'encerrada') DEFAULT 'ativa',
    destacada BOOLEAN DEFAULT FALSE,
    visualizacoes INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    INDEX idx_company (company_id),
    INDEX idx_status (status),
    INDEX idx_destacada (destacada),
    FULLTEXT idx_busca (titulo, descricao, requisitos)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: applications (Candidaturas)
CREATE TABLE IF NOT EXISTS applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    job_id INT NOT NULL,
    status ENUM('pendente', 'em_analise', 'aprovada', 'rejeitada', 'cancelada') DEFAULT 'pendente',
    mensagem TEXT,
    curriculo_path VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_application (user_id, job_id),
    INDEX idx_user (user_id),
    INDEX idx_job (job_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela: admin_logs (Logs Administrativos - Opcional)
CREATE TABLE IF NOT EXISTS admin_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NOT NULL,
    acao VARCHAR(100) NOT NULL,
    tabela_afetada VARCHAR(50),
    registro_id INT,
    dados_anteriores JSON,
    dados_novos JSON,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_admin (admin_id),
    INDEX idx_acao (acao),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criar usuário administrador padrão (senha: admin123 - ALTERAR EM PRODUÇÃO!)
-- Email: admin@vigged.com
-- Senha: admin123 (hash gerado com password_hash)
INSERT INTO users (nome, email, senha, tipo, status, email_verificado) 
VALUES (
    'Administrador',
    'admin@vigged.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- hash de 'admin123'
    'admin',
    'ativo',
    TRUE
) ON DUPLICATE KEY UPDATE nome=nome;

