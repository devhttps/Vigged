<?php
/**
 * Proteção CSRF
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once __DIR__ . '/../config/auth.php';

/**
 * Gera um token CSRF e armazena na sessão
 * @return string Token CSRF
 */
function generateCSRFToken() {
    startSecureSession();
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Retorna o token CSRF atual ou gera um novo
 * @return string Token CSRF
 */
function getCSRFToken() {
    return generateCSRFToken();
}

/**
 * Valida um token CSRF
 * @param string $token Token a ser validado
 * @return bool True se válido, false caso contrário
 */
function validateCSRFToken($token) {
    startSecureSession();
    
    if (!isset($_SESSION['csrf_token'])) {
        return false;
    }
    
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Verifica e valida token CSRF de requisição POST
 * Redireciona com erro se inválido
 * @param string $redirectUrl URL para redirecionar em caso de erro
 */
function requireCSRFToken($redirectUrl = 'index.php') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $token = $_POST['csrf_token'] ?? '';
        
        if (!validateCSRFToken($token)) {
            $_SESSION['csrf_error'] = 'Token de segurança inválido. Por favor, tente novamente.';
            header('Location: ' . $redirectUrl);
            exit;
        }
    }
}

/**
 * Gera campo hidden com token CSRF para formulários
 * @return string HTML do campo hidden
 */
function csrfField() {
    $token = getCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}

