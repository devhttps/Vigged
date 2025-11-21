<?php
/**
 * API para listar vagas de uma empresa
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

startSecureSession();
requireAuth(USER_TYPE_COMPANY);

header('Content-Type: application/json');

$currentUser = getCurrentUser();
$company_id = $currentUser['id'] ?? null;

if (!$company_id) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autenticado']);
    exit;
}

$pdo = getDBConnection();
if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro de conexão']);
    exit;
}

try {
    $status = isset($_GET['status']) ? $_GET['status'] : 'ativa';
    
    $stmt = $pdo->prepare("
        SELECT 
            j.*,
            COUNT(a.id) as total_candidaturas,
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
    $vagas = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $vagas
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar vagas da empresa: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar vagas']);
    exit;
}

