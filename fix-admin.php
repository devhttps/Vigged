<?php
/**
 * Script para corrigir/criar administrador
 * Vigged - Plataforma de Inclusão e Oportunidades
 * 
 * ⚠️ Execute este arquivo apenas uma vez e depois remova-o!
 */

require_once 'config/database.php';
require_once 'config/constants.php';

$pdo = getDBConnection();

if (!$pdo) {
    die("❌ Erro ao conectar ao banco de dados. Verifique config/database.php");
}

echo "<h1>Corrigir/Criar Administrador</h1>";

// Verificar se admin existe
$stmt = $pdo->prepare("SELECT id, nome, email, tipo, status FROM users WHERE tipo = 'admin'");
$stmt->execute();
$admin = $stmt->fetch();

if ($admin) {
    echo "<p>✅ Administrador encontrado: {$admin['email']} (Status: {$admin['status']})</p>";
    
    // Atualizar senha para admin123
    $newPassword = 'admin123';
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("UPDATE users SET senha = ?, status = 'ativo' WHERE id = ?");
    $stmt->execute([$passwordHash, $admin['id']]);
    
    echo "<p>✅ Senha atualizada para: <strong>admin123</strong></p>";
    echo "<p>✅ Status atualizado para: <strong>ativo</strong></p>";
} else {
    echo "<p>❌ Administrador não encontrado. Criando...</p>";
    
    // Criar novo admin
    $nome = 'Administrador';
    $email = 'admin@vigged.com';
    $senha = 'admin123';
    $passwordHash = password_hash($senha, PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO users (nome, email, senha, tipo, status, email_verificado) 
            VALUES (?, ?, ?, 'admin', 'ativo', TRUE)
        ");
        $stmt->execute([$nome, $email, $passwordHash]);
        
        echo "<p>✅ Administrador criado com sucesso!</p>";
        echo "<p><strong>Email:</strong> $email</p>";
        echo "<p><strong>Senha:</strong> $senha</p>";
    } catch (PDOException $e) {
        echo "<p>❌ Erro ao criar administrador: " . $e->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<p><strong>⚠️ IMPORTANTE:</strong> Remova este arquivo após usar!</p>";
echo "<p><a href='login.php'>Ir para Login</a></p>";
?>

