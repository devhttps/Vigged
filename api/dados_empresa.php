<?php
/**
 * API para obter dados da empresa logada
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../config/auth.php';

startSecureSession();

// Verificar autenticação manualmente para melhor debug
if (!isAuthenticated()) {
    error_log("Usuário não autenticado - SESSION: " . print_r($_SESSION, true));
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Não autenticado']);
    exit;
}

if (!isUserType(USER_TYPE_COMPANY)) {
    error_log("Usuário não é empresa - Tipo: " . ($_SESSION['user_type'] ?? 'não definido'));
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Acesso negado']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$currentUser = getCurrentUser();
$company_id = $currentUser['id'] ?? null;

if (!$currentUser || !$company_id) {
    error_log("Erro de autenticação - currentUser: " . print_r($currentUser, true));
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Não autenticado']);
    exit;
}

$pdo = getDBConnection();
if (!$pdo) {
    error_log("Erro de conexão com banco de dados");
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro de conexão']);
    exit;
}

try {
    error_log("Buscando dados da empresa ID: " . $company_id);
    
    // Primeiro, buscar dados básicos da empresa
    $stmt = $pdo->prepare("
        SELECT 
            c.*
        FROM companies c
        WHERE c.id = :company_id
    ");
    
    $stmt->execute([':company_id' => $company_id]);
    $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$empresa) {
        error_log("Empresa não encontrada com ID: " . $company_id);
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Empresa não encontrada']);
        exit;
    }
    
    error_log("Empresa encontrada: " . ($empresa['nome_fantasia'] ?? $empresa['razao_social'] ?? 'Sem nome'));
    
    // Buscar estatísticas separadamente
    $statsStmt = $pdo->prepare("
        SELECT 
            COUNT(DISTINCT j.id) as total_vagas,
            COUNT(DISTINCT CASE WHEN j.status = 'ativa' THEN j.id END) as vagas_ativas,
            COUNT(DISTINCT a.id) as total_candidaturas
        FROM companies c
        LEFT JOIN jobs j ON c.id = j.company_id
        LEFT JOIN applications a ON j.id = a.job_id
        WHERE c.id = :company_id
    ");
    
    $statsStmt->execute([':company_id' => $company_id]);
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
    
    // Mesclar dados
    $empresa['total_vagas'] = (int)($stats['total_vagas'] ?? 0);
    $empresa['vagas_ativas'] = (int)($stats['vagas_ativas'] ?? 0);
    $empresa['total_candidaturas'] = (int)($stats['total_candidaturas'] ?? 0);
    
    // Remover senha da resposta
    unset($empresa['senha']);
    
    echo json_encode([
        'success' => true,
        'data' => $empresa
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar dados da empresa: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao buscar dados: ' . $e->getMessage()]);
    exit;
}

