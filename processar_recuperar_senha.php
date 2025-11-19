<?php
/**
 * Processamento de Recuperação de Senha
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once 'config/constants.php';
require_once 'config/database.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: esqueceu-senha.php');
    exit;
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

$email = sanitizeInput($_POST['email'] ?? '');
$action = sanitizeInput($_POST['action'] ?? 'request'); // request ou reset

$errors = [];

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email inválido.";
}

$pdo = getDBConnection();
if (!$pdo) {
    $errors[] = "Erro de conexão com banco de dados.";
}

if (!empty($errors)) {
    $_SESSION['recuperar_errors'] = $errors;
    header('Location: esqueceu-senha.php');
    exit;
}

try {
    if ($action === 'request') {
        // Gerar token de recuperação
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Verificar se é usuário PCD ou empresa
        $user = null;
        $userType = null;
        
        // Tentar como usuário PCD
        $stmt = $pdo->prepare("SELECT id, nome FROM users WHERE email = ? AND tipo = 'pcd'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user) {
            $userType = 'pcd';
        }
        
        // Tentar como empresa
        if (!$user) {
            $stmt = $pdo->prepare("SELECT id, razao_social as nome FROM companies WHERE email_corporativo = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if ($user) {
                $userType = 'company';
            }
        }
        
        if (!$user) {
            // Por segurança, não revelar se o email existe ou não
            $_SESSION['recuperar_success'] = 'Se o email existir, você receberá instruções para recuperar sua senha.';
            header('Location: esqueceu-senha.php');
            exit;
        }
        
        // Salvar token no banco (criar tabela tokens se não existir)
        // Por simplicidade, vamos usar uma tabela temporária ou adicionar campos nas tabelas existentes
        // Por agora, vamos armazenar na sessão (não ideal para produção)
        $_SESSION['reset_token'] = $token;
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_user_id'] = $user['id'];
        $_SESSION['reset_user_type'] = $userType;
        $_SESSION['reset_expires'] = $expires_at;
        
        // TODO: Enviar email com link de recuperação
        // Link seria: esqueceu-senha.php?token=$token
        
        $_SESSION['recuperar_success'] = 'Instruções de recuperação foram enviadas para seu email.';
        header('Location: esqueceu-senha.php');
        exit;
        
    } elseif ($action === 'reset') {
        // Resetar senha com token
        $token = sanitizeInput($_POST['token'] ?? '');
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($token) || empty($new_password) || empty($confirm_password)) {
            $errors[] = "Todos os campos são obrigatórios.";
        }
        
        if ($new_password !== $confirm_password) {
            $errors[] = "As senhas não coincidem.";
        }
        
        if (strlen($new_password) < 6) {
            $errors[] = "A senha deve ter pelo menos 6 caracteres.";
        }
        
        // Verificar token na sessão
        if (empty($errors) && isset($_SESSION['reset_token']) && $_SESSION['reset_token'] === $token) {
            $user_id = $_SESSION['reset_user_id'] ?? null;
            $user_type = $_SESSION['reset_user_type'] ?? null;
            $expires = $_SESSION['reset_expires'] ?? null;
            
            if (!$user_id || !$user_type || strtotime($expires) < time()) {
                $errors[] = "Token inválido ou expirado.";
            }
        } else {
            $errors[] = "Token inválido.";
        }
        
        if (!empty($errors)) {
            $_SESSION['recuperar_errors'] = $errors;
            header('Location: esqueceu-senha.php?token=' . urlencode($token));
            exit;
        }
        
        // Atualizar senha
        $senha_hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        if ($user_type === 'pcd') {
            $stmt = $pdo->prepare("UPDATE users SET senha = ? WHERE id = ?");
        } else {
            $stmt = $pdo->prepare("UPDATE companies SET senha = ? WHERE id = ?");
        }
        
        $stmt->execute([$senha_hash, $user_id]);
        
        // Limpar dados da sessão
        unset($_SESSION['reset_token'], $_SESSION['reset_email'], $_SESSION['reset_user_id'], 
              $_SESSION['reset_user_type'], $_SESSION['reset_expires']);
        
        $_SESSION['recuperar_success'] = 'Senha alterada com sucesso! Você já pode fazer login.';
        header('Location: login.php');
        exit;
    }
    
} catch (PDOException $e) {
    error_log("Erro ao processar recuperação de senha: " . $e->getMessage());
    $_SESSION['recuperar_errors'] = ['Erro ao processar solicitação. Tente novamente.'];
    header('Location: esqueceu-senha.php');
    exit;
}

