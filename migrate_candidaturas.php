<?php
/**
 * Script de Migração: Adicionar campos de feedback e avaliação
 * Execute este arquivo uma vez para atualizar o banco de dados
 * 
 * INSTRUÇÕES:
 * 1. Acesse via navegador: http://localhost/vigged/migrate_candidaturas.php
 * 2. Ou execute via linha de comando: php migrate_candidaturas.php
 * 3. Após executar com sucesso, DELETE este arquivo por segurança
 */

require_once 'config/database.php';

// Verificar se está sendo executado via navegador
if (php_sapi_name() !== 'cli') {
    header('Content-Type: text/html; charset=utf-8');
}

echo "<h1>Migração: Adicionar campos de feedback e avaliação</h1>";
echo "<pre>";

try {
    $pdo = getDBConnection();
    
    if (!$pdo) {
        throw new Exception("Erro ao conectar ao banco de dados");
    }
    
    echo "✓ Conexão com banco de dados estabelecida\n\n";
    
    // Verificar se as colunas já existem
    $stmt = $pdo->query("SHOW COLUMNS FROM applications LIKE 'feedback'");
    $feedbackExists = $stmt->rowCount() > 0;
    
    $stmt = $pdo->query("SHOW COLUMNS FROM applications LIKE 'avaliacao'");
    $avaliacaoExists = $stmt->rowCount() > 0;
    
    $stmt = $pdo->query("SHOW COLUMNS FROM applications LIKE 'avaliado_em'");
    $avaliadoEmExists = $stmt->rowCount() > 0;
    
    if ($feedbackExists && $avaliacaoExists && $avaliadoEmExists) {
        echo "✓ Campos já existem na tabela applications\n";
    } else {
        echo "Adicionando campos à tabela applications...\n";
        
        if (!$feedbackExists) {
            $pdo->exec("ALTER TABLE applications ADD COLUMN feedback TEXT NULL AFTER mensagem");
            echo "✓ Campo 'feedback' adicionado\n";
        }
        
        if (!$avaliacaoExists) {
            $pdo->exec("ALTER TABLE applications ADD COLUMN avaliacao TINYINT NULL CHECK (avaliacao >= 1 AND avaliacao <= 5) AFTER feedback");
            echo "✓ Campo 'avaliacao' adicionado\n";
        }
        
        if (!$avaliadoEmExists) {
            $pdo->exec("ALTER TABLE applications ADD COLUMN avaliado_em TIMESTAMP NULL AFTER avaliacao");
            echo "✓ Campo 'avaliado_em' adicionado\n";
        }
    }
    
    // Verificar se a tabela notifications existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'notifications'");
    $notificationsExists = $stmt->rowCount() > 0;
    
    if (!$notificationsExists) {
        echo "\nCriando tabela notifications...\n";
        $pdo->exec("
            CREATE TABLE notifications (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "✓ Tabela 'notifications' criada\n";
    } else {
        echo "✓ Tabela 'notifications' já existe\n";
    }
    
    // Verificar se a tabela application_status_history existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'application_status_history'");
    $historyExists = $stmt->rowCount() > 0;
    
    if (!$historyExists) {
        echo "\nCriando tabela application_status_history...\n";
        $pdo->exec("
            CREATE TABLE application_status_history (
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "✓ Tabela 'application_status_history' criada\n";
    } else {
        echo "✓ Tabela 'application_status_history' já existe\n";
    }
    
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

