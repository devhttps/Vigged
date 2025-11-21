-- Migration: Adicionar campos de perfil completo para PCD
-- Execute este script para adicionar campos que faltam na tabela users

USE vigged_db;

-- Adicionar campos de perfil profissional
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS foto_perfil VARCHAR(500) NULL AFTER laudo_medico_path,
ADD COLUMN IF NOT EXISTS sobre TEXT NULL AFTER outras_necessidades,
ADD COLUMN IF NOT EXISTS habilidades JSON NULL AFTER sobre,
ADD COLUMN IF NOT EXISTS curriculo_path VARCHAR(500) NULL AFTER habilidades,
ADD COLUMN IF NOT EXISTS formacao_academica JSON NULL AFTER curriculo_path,
ADD COLUMN IF NOT EXISTS experiencias JSON NULL AFTER formacao_academica;

-- Adicionar campos de endereço
ALTER TABLE users
ADD COLUMN IF NOT EXISTS cep VARCHAR(9) NULL AFTER telefone,
ADD COLUMN IF NOT EXISTS estado VARCHAR(2) NULL AFTER cep,
ADD COLUMN IF NOT EXISTS cidade VARCHAR(100) NULL AFTER estado,
ADD COLUMN IF NOT EXISTS bairro VARCHAR(100) NULL AFTER cidade,
ADD COLUMN IF NOT EXISTS logradouro VARCHAR(255) NULL AFTER bairro,
ADD COLUMN IF NOT EXISTS numero VARCHAR(20) NULL AFTER logradouro,
ADD COLUMN IF NOT EXISTS complemento VARCHAR(100) NULL AFTER numero;

-- Criar índices para melhor performance
CREATE INDEX IF NOT EXISTS idx_cidade ON users(cidade);
CREATE INDEX IF NOT EXISTS idx_estado ON users(estado);

