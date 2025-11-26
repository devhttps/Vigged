<?php
/**
 * API para listar vagas de uma empresa
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

startSecureSession();

// Verificar autenticação manualmente para melhor debug
if (!isAuthenticated()) {
    error_log("Usuário não autenticado ao buscar vagas - SESSION: " . print_r($_SESSION, true));
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Não autenticado']);
    exit;
}

if (!isUserType(USER_TYPE_COMPANY)) {
    error_log("Usuário não é empresa ao buscar vagas - Tipo: " . ($_SESSION['user_type'] ?? 'não definido'));
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Acesso negado']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

$currentUser = getCurrentUser();
$company_id = $currentUser['id'] ?? null;

if (!$currentUser || !$company_id) {
    error_log("Erro de autenticação ao buscar vagas - currentUser: " . print_r($currentUser, true));
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Não autenticado']);
    exit;
}

$pdo = getDBConnection();
if (!$pdo) {
    error_log("Erro de conexão com banco de dados ao buscar vagas");
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro de conexão']);
    exit;
}

try {
    $status = isset($_GET['status']) ? $_GET['status'] : 'ativa';
    error_log("Buscando vagas da empresa ID: " . $company_id . " com status: " . $status);
    
    $stmt = $pdo->prepare("
        SELECT 
            j.*,
            COUNT(DISTINCT a.id) as total_candidaturas,
            SUM(CASE WHEN a.status = 'pendente' THEN 1 ELSE 0 END) as candidaturas_pendentes
        FROM jobs j
        LEFT JOIN applications a ON j.id = a.job_id
        WHERE j.company_id = :company_id
        " . ($status !== 'todas' ? "AND j.status = :status" : "") . "
        GROUP BY j.id
        ORDER BY j.created_at DESC
    ");
    
    $stmt->bindValue(':company_id', $company_id, PDO::PARAM_INT);
    if ($status !== 'todas') {
        $stmt->bindValue(':status', $status);
    }
    
    $stmt->execute();
    $vagas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    error_log("Vagas encontradas: " . count($vagas));
    
    echo json_encode([
        'success' => true,
        'data' => $vagas
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar vagas da empresa: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro ao buscar vagas: ' . $e->getMessage()]);
    exit;
}

