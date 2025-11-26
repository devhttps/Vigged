<?php
/**
 * Funções de Autenticação e Autorização
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/database.php';

/**
 * Inicia sessão segura
 */
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 0); // 1 em produção com HTTPS
        session_name(SESSION_NAME);
        session_start();
        
        // Regenera ID da sessão periodicamente
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } else if (time() - $_SESSION['created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
}

/**
 * Verifica se usuário está autenticado
 * @return bool True se autenticado, false caso contrário
 */
function isAuthenticated() {
    startSecureSession();
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

/**
 * Verifica se usuário é do tipo especificado
 * @param string $type Tipo de usuário (pcd, company, admin)
 * @return bool True se for do tipo, false caso contrário
 */
function isUserType($type) {
    if (!isAuthenticated()) {
        return false;
    }
    return $_SESSION['user_type'] === $type;
}

/**
 * Verifica se usuário é administrador
 * @return bool True se for admin, false caso contrário
 */
function isAdmin() {
    return isUserType(USER_TYPE_ADMIN);
}

/**
 * Verifica se usuário é candidato PCD
 * @return bool True se for PCD, false caso contrário
 */
function isPCD() {
    return isUserType(USER_TYPE_PCD);
}

/**
 * Verifica se usuário é empresa
 * @return bool True se for empresa, false caso contrário
 */
function isCompany() {
    return isUserType(USER_TYPE_COMPANY);
}

/**
 * Requer autenticação, redireciona se não autenticado
 * @param string|null $requiredType Tipo de usuário requerido (opcional)
 */
function requireAuth($requiredType = null) {
    if (!isAuthenticated()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
    
    if ($requiredType !== null && !isUserType($requiredType)) {
        header('Location: ' . BASE_URL . '/index.php');
        exit;
    }
}

/**
 * Requer que usuário seja administrador
 */
function requireAdmin() {
    requireAuth(USER_TYPE_ADMIN);
}

/**
 * Faz login do usuário
 * @param int $userId ID do usuário
 * @param string $userType Tipo do usuário
 * @param array $additionalData Dados adicionais para sessão
 */
function login($userId, $userType, $additionalData = []) {
    startSecureSession();
    
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_type'] = $userType;
    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();
    
    foreach ($additionalData as $key => $value) {
        $_SESSION[$key] = $value;
    }
    
    session_regenerate_id(true);
}

/**
 * Faz logout do usuário
 */
function logout() {
    startSecureSession();
    
    $_SESSION = [];
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
}

/**
 * Valida credenciais de login
 * @param string $email Email do usuário
 * @param string $password Senha em texto plano
 * @param string $userType Tipo de usuário esperado
 * @return array|false Retorna dados do usuário ou false se inválido
 */
function validateCredentials($email, $password, $userType) {
    $pdo = getDBConnection();
    if (!$pdo) {
        return false;
    }
    
    try {
        if ($userType === USER_TYPE_COMPANY) {
            $stmt = $pdo->prepare("SELECT id, email_corporativo as email, senha, razao_social as nome, status FROM companies WHERE email_corporativo = ? AND status != 'inativa'");
            $stmt->execute([$email]);
        } else {
            $stmt = $pdo->prepare("SELECT id, email, senha, nome, tipo, status FROM users WHERE email = ? AND tipo = ? AND status != 'inativo'");
            $stmt->execute([$email, $userType]);
        }
        
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['senha'])) {
            return $user;
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Erro ao validar credenciais: " . $e->getMessage());
        return false;
    }
}

/**
 * Obtém dados do usuário logado
 * @return array|null Dados do usuário ou null se não autenticado
 */
function getCurrentUser() {
    if (!isAuthenticated()) {
        return null;
    }
    
    $pdo = getDBConnection();
    if (!$pdo) {
        return null;
    }
    
    try {
        $userId = $_SESSION['user_id'];
        $userType = $_SESSION['user_type'];
        
        if ($userType === USER_TYPE_COMPANY) {
            $stmt = $pdo->prepare("SELECT * FROM companies WHERE id = ?");
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        }
        
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Erro ao obter usuário atual: " . $e->getMessage());
        return null;
    }
}

