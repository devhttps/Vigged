-- Migration: Adicionar campos de feedback, avaliação e criar tabela de notificações
-- Execute este script para adicionar funcionalidades de gerenciamento de candidaturas

USE vigged_db;

-- Adicionar campos de feedback e avaliação na tabela applications
ALTER TABLE applications 
ADD COLUMN IF NOT EXISTS feedback TEXT NULL AFTER mensagem,
ADD COLUMN IF NOT EXISTS avaliacao TINYINT NULL CHECK (avaliacao >= 1 AND avaliacao <= 5) AFTER feedback,
ADD COLUMN IF NOT EXISTS avaliado_em TIMESTAMP NULL AFTER avaliacao;

-- Criar tabela de notificações
CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    tipo ENUM('candidatura', 'status', 'avaliacao', 'sistema') DEFAULT 'candidatura',
    titulo VARCHAR(255) NOT NULL,
    mensagem TEXT NOT NULL,
    link VARCHAR(500) NULL,
    lida BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_lida (lida),
    INDEX idx_tipo (tipo),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Criar tabela de histórico de mudanças de status
CREATE TABLE IF NOT EXISTS application_status_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    status_anterior VARCHAR(50),
    status_novo VARCHAR(50) NOT NULL,
    feedback TEXT NULL,
    avaliacao TINYINT NULL,
    changed_by INT NULL COMMENT 'ID do usuário que fez a mudança (empresa ou admin)',
    changed_by_type ENUM('company', 'admin') NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    INDEX idx_application (application_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

