<?php
/**
 * Exemplo de Configuração do Banco de Dados
 * Vigged - Plataforma de Inclusão e Oportunidades
 * 
 * INSTRUÇÕES:
 * 1. Copie este arquivo para database.php
 * 2. Ajuste as configurações conforme seu ambiente
 * 3. NUNCA commite o arquivo database.php com credenciais reais no Git
 */

// Configurações do banco de dados
define('DB_HOST', 'localhost');        // Host do banco de dados
define('DB_NAME', 'vigged_db');        // Nome do banco de dados
define('DB_USER', 'root');             // Usuário do banco de dados
define('DB_PASS', '');                 // Senha do banco de dados
define('DB_CHARSET', 'utf8mb4');       // Charset (recomendado: utf8mb4)

/**
 * Conexão com o banco de dados usando PDO
 * @return PDO|null Retorna instância PDO ou null em caso de erro
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Erro de conexão com banco de dados: " . $e->getMessage());
            return null;
        }
    }
    
    return $pdo;
}

/**
 * Testa a conexão com o banco de dados
 * @return bool True se conectado, false caso contrário
 */
function testDBConnection() {
    $pdo = getDBConnection();
    return $pdo !== null;
}

