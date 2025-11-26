<?php
/**
 * Migração: Sistema de Aprovação de Planos
 * 
 * Esta migração cria a tabela plan_requests para gerenciar
 * solicitações de planos que precisam ser aprovadas pelo administrador.
 * 
 * Execute via navegador: http://localhost/vigged/migrate_planos.php
 * Ou via linha de comando: php migrate_planos.php
 */

require_once 'config/database.php';

header('Content-Type: text/html; charset=utf-8');

if (php_sapi_name() !== 'cli') {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Migração - Planos</title></head><body>";
    echo "<h1>Migração: Sistema de Aprovação de Planos</h1>";
    echo "<pre>";
}

try {
    $pdo = getDBConnection();
    
    if (!$pdo) {
        throw new Exception("Não foi possível conectar ao banco de dados.");
    }
    
    echo "Iniciando migração...\n\n";
    
    // Verificar se a tabela plan_requests existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'plan_requests'");
    $tableExists = $stmt->rowCount() > 0;
    
    if (!$tableExists) {
        echo "Criando tabela plan_requests...\n";
        $pdo->exec("
            CREATE TABLE plan_requests (
                id INT PRIMARY KEY AUTO_INCREMENT,
                company_id INT NOT NULL,
                plano_solicitado ENUM('essencial', 'profissional', 'enterprise') NOT NULL,
                valor DECIMAL(10, 2) NOT NULL,
                status ENUM('pendente', 'aprovado', 'rejeitado') DEFAULT 'pendente',
                observacoes TEXT NULL,
                motivo_rejeicao TEXT NULL,
                aprovado_por INT NULL COMMENT 'ID do administrador que aprovou',
                aprovado_em TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                
                FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
                FOREIGN KEY (aprovado_por) REFERENCES users(id) ON DELETE SET NULL,
                INDEX idx_company (company_id),
                INDEX idx_status (status),
                INDEX idx_plano (plano_solicitado),
                INDEX idx_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "✓ Tabela 'plan_requests' criada\n";
    } else {
        echo "✓ Tabela 'plan_requests' já existe\n";
    }
    
    // Verificar se a coluna plano_status existe na tabela companies
    $stmt = $pdo->query("SHOW COLUMNS FROM companies LIKE 'plano_status'");
    $columnExists = $stmt->rowCount() > 0;
    
    if (!$columnExists) {
        echo "\nAdicionando coluna plano_status na tabela companies...\n";
        $pdo->exec("
            ALTER TABLE companies 
            ADD COLUMN plano_status ENUM('ativo', 'pendente', 'cancelado', 'expirado') DEFAULT 'ativo' 
            AFTER plano
        ");
        echo "✓ Coluna 'plano_status' adicionada\n";
    } else {
        echo "✓ Coluna 'plano_status' já existe\n";
    }
    
    // Atualizar plano_status para 'ativo' em empresas com plano diferente de 'gratuito'
    echo "\nAtualizando status de planos existentes...\n";
    $pdo->exec("
        UPDATE companies 
        SET plano_status = 'ativo' 
        WHERE plano != 'gratuito' AND (plano_status IS NULL OR plano_status = '')
    ");
    echo "✓ Status de planos atualizado\n";
    
    echo "\n✅ Migração concluída com sucesso!\n";
    echo "\nVocê pode fechar esta página agora.\n";
    
} catch (PDOException $e) {
    echo "\n❌ Erro ao executar migração: " . $e->getMessage() . "\n";
    echo "Código SQL: " . $e->getCode() . "\n";
} catch (Exception $e) {
    echo "\n❌ Erro: " . $e->getMessage() . "\n";
}

if (php_sapi_name() !== 'cli') {
    echo "</pre>";
    echo "<p><strong>IMPORTANTE:</strong> Após verificar que tudo funcionou, delete este arquivo por segurança!</p>";
    echo "</body></html>";
}
?>

