<?php
/**
 * API de Configurações Administrativas
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

header('Content-Type: application/json; charset=utf-8');
require_once '../config/auth.php';
require_once '../config/database.php';

startSecureSession();
requireAdmin();

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get':
            getSettings();
            break;
        case 'save':
            saveSettings();
            break;
        case 'system_info':
            getSystemInfo();
            break;
        case 'clear_cache':
            clearCache();
            break;
        default:
            echo json_encode(['success' => false, 'error' => 'Ação inválida']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

/**
 * Obter configurações do sistema
 */
function getSettings() {
    $settingsFile = '../config/settings.json';
    
    if (file_exists($settingsFile)) {
        $settings = json_decode(file_get_contents($settingsFile), true);
    } else {
        // Configurações padrão
        $settings = [
            'site_name' => 'Vigged',
            'site_description' => 'Plataforma de Inclusão e Oportunidades',
            'base_url' => BASE_URL,
            'timezone' => 'America/Sao_Paulo',
            'email' => [
                'smtp_host' => '',
                'smtp_port' => 587,
                'smtp_user' => '',
                'smtp_pass' => '',
                'from_email' => 'noreply@vigged.com.br',
                'from_name' => 'Vigged'
            ],
            'upload' => [
                'max_file_size' => 5242880, // 5MB
                'max_laudo_size' => 5242880, // 5MB
                'max_documento_size' => 10485760, // 10MB
                'allowed_image_types' => ['image/jpeg', 'image/png', 'image/jpg'],
                'allowed_doc_types' => ['application/pdf']
            ],
            'security' => [
                'password_min_length' => 8,
                'login_max_attempts' => 5,
                'login_lockout_time' => 900, // 15 minutos
                'session_lifetime' => 86400 // 24 horas
            ],
            'pagination' => [
                'items_per_page' => 10,
                'admin_items_per_page' => 20
            ]
        ];
    }
    
    echo json_encode(['success' => true, 'data' => $settings]);
}

/**
 * Salvar configurações do sistema
 */
function saveSettings() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'error' => 'Método não permitido']);
        return;
    }
    
    $settingsFile = '../config/settings.json';
    $configDir = dirname($settingsFile);
    
    // Garantir que o diretório existe e é gravável
    if (!is_dir($configDir)) {
        mkdir($configDir, 0755, true);
    }
    
    if (!is_writable($configDir)) {
        echo json_encode(['success' => false, 'error' => 'Diretório config/ não é gravável']);
        return;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
        return;
    }
    
    // Validar dados
    $settings = [
        'site_name' => sanitizeInput($data['site_name'] ?? 'Vigged'),
        'site_description' => sanitizeInput($data['site_description'] ?? ''),
        'base_url' => filter_var($data['base_url'] ?? BASE_URL, FILTER_SANITIZE_URL),
        'timezone' => sanitizeInput($data['timezone'] ?? 'America/Sao_Paulo'),
        'email' => [
            'smtp_host' => sanitizeInput($data['email']['smtp_host'] ?? ''),
            'smtp_port' => (int)($data['email']['smtp_port'] ?? 587),
            'smtp_user' => sanitizeInput($data['email']['smtp_user'] ?? ''),
            'smtp_pass' => sanitizeInput($data['email']['smtp_pass'] ?? ''),
            'from_email' => filter_var($data['email']['from_email'] ?? 'noreply@vigged.com.br', FILTER_SANITIZE_EMAIL),
            'from_name' => sanitizeInput($data['email']['from_name'] ?? 'Vigged')
        ],
        'upload' => [
            'max_file_size' => (int)($data['upload']['max_file_size'] ?? 5242880),
            'max_laudo_size' => (int)($data['upload']['max_laudo_size'] ?? 5242880),
            'max_documento_size' => (int)($data['upload']['max_documento_size'] ?? 10485760),
            'allowed_image_types' => $data['upload']['allowed_image_types'] ?? ['image/jpeg', 'image/png', 'image/jpg'],
            'allowed_doc_types' => $data['upload']['allowed_doc_types'] ?? ['application/pdf']
        ],
        'security' => [
            'password_min_length' => (int)($data['security']['password_min_length'] ?? 8),
            'login_max_attempts' => (int)($data['security']['login_max_attempts'] ?? 5),
            'login_lockout_time' => (int)($data['security']['login_lockout_time'] ?? 900),
            'session_lifetime' => (int)($data['security']['session_lifetime'] ?? 86400)
        ],
        'pagination' => [
            'items_per_page' => (int)($data['pagination']['items_per_page'] ?? 10),
            'admin_items_per_page' => (int)($data['pagination']['admin_items_per_page'] ?? 20)
        ],
        'updated_at' => date('Y-m-d H:i:s'),
        'updated_by' => getCurrentUser()['id'] ?? null
    ];
    
    // Salvar em arquivo JSON
    $result = file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    if ($result === false) {
        echo json_encode(['success' => false, 'error' => 'Erro ao salvar configurações']);
        return;
    }
    
    // Log da ação
    logAdminAction('configuracoes', 'update', 'Configurações atualizadas');
    
    echo json_encode(['success' => true, 'message' => 'Configurações salvas com sucesso']);
}

/**
 * Obter informações do sistema
 */
function getSystemInfo() {
    $info = [
        'php_version' => phpversion(),
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido',
        'php_extensions' => [
            'pdo' => extension_loaded('pdo'),
            'pdo_mysql' => extension_loaded('pdo_mysql'),
            'mbstring' => extension_loaded('mbstring'),
            'fileinfo' => extension_loaded('fileinfo'),
            'json' => extension_loaded('json'),
            'gd' => extension_loaded('gd'),
            'curl' => extension_loaded('curl')
        ],
        'php_settings' => [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_execution_time' => ini_get('max_execution_time'),
            'memory_limit' => ini_get('memory_limit')
        ],
        'database' => [
            'connected' => false,
            'version' => null
        ],
        'directories' => [
            'config_writable' => is_writable('../config'),
            'uploads_writable' => is_writable('../uploads'),
            'uploads_size' => getDirectorySize('../uploads')
        ],
        'last_update' => date('Y-m-d H:i:s')
    ];
    
    // Verificar conexão com banco
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->query('SELECT VERSION() as version');
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $info['database']['connected'] = true;
        $info['database']['version'] = $result['version'] ?? 'Desconhecido';
    } catch (Exception $e) {
        $info['database']['error'] = $e->getMessage();
    }
    
    echo json_encode(['success' => true, 'data' => $info]);
}

/**
 * Limpar cache e logs
 */
function clearCache() {
    $cleared = [];
    $errors = [];
    
    // Limpar logs antigos (mais de 30 dias)
    $logsDir = '../logs';
    if (is_dir($logsDir)) {
        $files = glob($logsDir . '/*.log');
        foreach ($files as $file) {
            if (filemtime($file) < strtotime('-30 days')) {
                if (unlink($file)) {
                    $cleared[] = 'Logs antigos';
                } else {
                    $errors[] = 'Erro ao limpar logs';
                }
            }
        }
    }
    
    // Limpar sessões antigas (se houver diretório de sessões)
    if (ini_get('session.save_handler') === 'files') {
        $sessionPath = session_save_path();
        if ($sessionPath && is_dir($sessionPath)) {
            $files = glob($sessionPath . '/sess_*');
            $clearedCount = 0;
            foreach ($files as $file) {
                if (filemtime($file) < strtotime('-7 days')) {
                    if (unlink($file)) {
                        $clearedCount++;
                    }
                }
            }
            if ($clearedCount > 0) {
                $cleared[] = "$clearedCount sessões antigas";
            }
        }
    }
    
    if (empty($cleared) && empty($errors)) {
        $cleared[] = 'Nada para limpar';
    }
    
    logAdminAction('configuracoes', 'clear_cache', 'Cache e logs limpos');
    
    echo json_encode([
        'success' => empty($errors),
        'cleared' => $cleared,
        'errors' => $errors,
        'message' => empty($errors) ? 'Cache limpo com sucesso' : 'Alguns erros ocorreram'
    ]);
}

/**
 * Função auxiliar para sanitizar input
 * Usa função do includes/functions.php se disponível, senão usa função local
 */
if (!function_exists('sanitizeInput')) {
    function sanitizeInput($data) {
        return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Função auxiliar para calcular tamanho de diretório
 */
function getDirectorySize($directory) {
    $size = 0;
    if (is_dir($directory)) {
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)) as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
    }
    return formatBytes($size);
}

/**
 * Formatar bytes para formato legível
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Log de ações administrativas
 */
function logAdminAction($module, $action, $description) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('INSERT INTO admin_logs (user_id, module, action, description, ip_address, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            getCurrentUser()['id'] ?? null,
            $module,
            $action,
            $description,
            $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
        ]);
    } catch (Exception $e) {
        // Silenciar erros de log
    }
}

