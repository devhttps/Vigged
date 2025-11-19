<?php
/**
 * Processamento de Login
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once 'config/constants.php';
require_once 'config/database.php';
require_once 'config/auth.php';

// Iniciar sessão
startSecureSession();

// Verificar se é requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Coletar dados do formulário
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$userType = $_POST['user_type'] ?? 'pcd'; // pcd, company ou admin

// Validações básicas
$errors = [];

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Email inválido.";
}

if (empty($password)) {
    $errors[] = "Senha é obrigatória.";
}

// Se houver erros, redirecionar de volta
if (!empty($errors)) {
    $_SESSION['login_errors'] = $errors;
    header('Location: login.php');
    exit;
}

// Tentar autenticar - detectar tipo automaticamente
$user = null;
$finalUserType = null;

// Tentar como Admin primeiro
$adminUser = validateCredentials($email, $password, USER_TYPE_ADMIN);
if ($adminUser) {
    $user = $adminUser;
    $finalUserType = USER_TYPE_ADMIN;
}

// Tentar como Empresa se não encontrou admin
if (!$user) {
    $companyUser = validateCredentials($email, $password, USER_TYPE_COMPANY);
    if ($companyUser) {
        $user = $companyUser;
        $finalUserType = USER_TYPE_COMPANY;
    }
}

// Tentar como PCD se não encontrou
if (!$user) {
    $pcdUser = validateCredentials($email, $password, USER_TYPE_PCD);
    if ($pcdUser) {
        $user = $pcdUser;
        $finalUserType = USER_TYPE_PCD;
    }
}

// Se encontrou usuário, fazer login
if ($user) {
    // Verificar se usuário está ativo
    $status = $user['status'] ?? '';
    
    if ($status === 'inativo' || $status === 'inativa') {
        $_SESSION['login_errors'] = ['Sua conta está inativa. Entre em contato com o suporte.'];
        header('Location: login.php');
        exit;
    }
    
    // Fazer login
    login($user['id'], $finalUserType, [
        'user_name' => $user['nome'] ?? '',
        'user_email' => $user['email'] ?? ''
    ]);
    
    // Redirecionar baseado no tipo
    if ($finalUserType === USER_TYPE_ADMIN) {
        header('Location: admin.php');
    } elseif ($finalUserType === USER_TYPE_COMPANY) {
        header('Location: perfil-empresa.php');
    } else {
        header('Location: perfil-pcd.php');
    }
    exit;
} else {
    // Credenciais inválidas - log para debug
    error_log("Tentativa de login falhou - Email: $email, Tipo tentado: $userType");
    
    // Verificar se o email existe no banco (para dar feedback melhor)
    $pdo = getDBConnection();
    if ($pdo) {
        try {
            // Verificar em users
            $stmt = $pdo->prepare("SELECT email, tipo, status FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $userExists = $stmt->fetch();
            
            // Verificar em companies
            if (!$userExists) {
                $stmt = $pdo->prepare("SELECT email_corporativo as email, status FROM companies WHERE email_corporativo = ?");
                $stmt->execute([$email]);
                $companyExists = $stmt->fetch();
            }
            
            if ($userExists || $companyExists) {
                $status = ($userExists ? $userExists['status'] : $companyExists['status']);
                if ($status === 'inativo' || $status === 'inativa') {
                    $_SESSION['login_errors'] = ['Sua conta está inativa. Entre em contato com o suporte.'];
                } else {
                    $_SESSION['login_errors'] = ['Email ou senha incorretos.'];
                }
            } else {
                $_SESSION['login_errors'] = ['Email ou senha incorretos.'];
            }
        } catch (PDOException $e) {
            error_log("Erro ao verificar email no login: " . $e->getMessage());
            $_SESSION['login_errors'] = ['Email ou senha incorretos.'];
        }
    } else {
        $_SESSION['login_errors'] = ['Erro de conexão. Tente novamente mais tarde.'];
    }
    
    header('Location: login.php');
    exit;
}

