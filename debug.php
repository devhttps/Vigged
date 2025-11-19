<?php
/**
 * Arquivo de Debug - Vigged
 * Use este arquivo para diagnosticar problemas
 * 
 * ⚠️ REMOVA ESTE ARQUIVO EM PRODUÇÃO!
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Debug - Vigged</h1>";

// Teste 1: Verificar configuração do banco
echo "<h2>1. Teste de Conexão com Banco</h2>";
require_once 'config/database.php';
$pdo = getDBConnection();
if ($pdo) {
    echo "✅ Conexão OK<br>";
    
    // Verificar tabelas
    $tables = ['users', 'companies', 'jobs', 'applications'];
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM $table");
            $result = $stmt->fetch();
            echo "✅ Tabela $table: {$result['total']} registros<br>";
        } catch (PDOException $e) {
            echo "❌ Erro na tabela $table: " . $e->getMessage() . "<br>";
        }
    }
    
    // Verificar admin
    echo "<h2>2. Verificar Administrador</h2>";
    try {
        $stmt = $pdo->prepare("SELECT id, nome, email, tipo, status FROM users WHERE tipo = 'admin'");
        $stmt->execute();
        $admins = $stmt->fetchAll();
        
        if (empty($admins)) {
            echo "❌ Nenhum administrador encontrado<br>";
        } else {
            foreach ($admins as $admin) {
                echo "✅ Admin encontrado: {$admin['email']} (Status: {$admin['status']})<br>";
                
                // Testar senha
                $testPassword = 'admin123';
                $stmt2 = $pdo->prepare("SELECT senha FROM users WHERE id = ?");
                $stmt2->execute([$admin['id']]);
                $userData = $stmt2->fetch();
                
                if ($userData && password_verify($testPassword, $userData['senha'])) {
                    echo "✅ Senha 'admin123' está correta<br>";
                } else {
                    echo "❌ Senha 'admin123' NÃO está correta<br>";
                    echo "Hash no banco: " . substr($userData['senha'], 0, 20) . "...<br>";
                    echo "Hash esperado: " . substr(password_hash($testPassword, PASSWORD_DEFAULT), 0, 20) . "...<br>";
                }
            }
        }
    } catch (PDOException $e) {
        echo "❌ Erro: " . $e->getMessage() . "<br>";
    }
    
    // Verificar últimos cadastros
    echo "<h2>3. Últimos Cadastros</h2>";
    try {
        $stmt = $pdo->query("SELECT id, nome, email, tipo, status, created_at FROM users ORDER BY created_at DESC LIMIT 5");
        $users = $stmt->fetchAll();
        
        if (empty($users)) {
            echo "❌ Nenhum usuário cadastrado<br>";
        } else {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>ID</th><th>Nome</th><th>Email</th><th>Tipo</th><th>Status</th><th>Data</th></tr>";
            foreach ($users as $user) {
                echo "<tr>";
                echo "<td>{$user['id']}</td>";
                echo "<td>{$user['nome']}</td>";
                echo "<td>{$user['email']}</td>";
                echo "<td>{$user['tipo']}</td>";
                echo "<td>{$user['status']}</td>";
                echo "<td>{$user['created_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } catch (PDOException $e) {
        echo "❌ Erro: " . $e->getMessage() . "<br>";
    }
    
} else {
    echo "❌ Erro na conexão<br>";
    echo "Verifique config/database.php<br>";
}

// Teste 2: Verificar sessões
echo "<h2>4. Teste de Sessões</h2>";
session_start();
echo "Session ID: " . session_id() . "<br>";
echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? "Ativa" : "Inativa") . "<br>";
if (isset($_SESSION)) {
    echo "Dados da sessão: <pre>" . print_r($_SESSION, true) . "</pre>";
}

// Teste 3: Verificar constantes
echo "<h2>5. Verificar Constantes</h2>";
require_once 'config/constants.php';
echo "BASE_URL: " . BASE_URL . "<br>";
echo "DB_HOST: " . DB_HOST . "<br>";
echo "DB_NAME: " . DB_NAME . "<br>";

// Teste 4: Verificar funções de autenticação
echo "<h2>6. Teste de Autenticação</h2>";
require_once 'config/auth.php';
echo "Função startSecureSession existe: " . (function_exists('startSecureSession') ? "Sim" : "Não") . "<br>";
echo "Função validateCredentials existe: " . (function_exists('validateCredentials') ? "Sim" : "Não") . "<br>";
echo "Função login existe: " . (function_exists('login') ? "Sim" : "Não") . "<br>";

echo "<hr>";
echo "<p><strong>⚠️ Lembre-se de remover este arquivo em produção!</strong></p>";
?>

