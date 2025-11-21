<?php
/**
 * API para obter dados da empresa logada
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../config/auth.php';

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
    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            COUNT(DISTINCT j.id) as total_vagas,
            COUNT(DISTINCT CASE WHEN j.status = 'ativa' THEN j.id END) as vagas_ativas,
            COUNT(DISTINCT a.id) as total_candidaturas
        FROM companies c
        LEFT JOIN jobs j ON c.id = j.company_id
        LEFT JOIN applications a ON j.id = a.job_id
        WHERE c.id = :company_id
        GROUP BY c.id
    ");
    
    $stmt->execute([':company_id' => $company_id]);
    $empresa = $stmt->fetch();
    
    if (!$empresa) {
        http_response_code(404);
        echo json_encode(['error' => 'Empresa não encontrada']);
        exit;
    }
    
    // Remover senha da resposta
    unset($empresa['senha']);
    
    echo json_encode([
        'success' => true,
        'data' => $empresa
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar dados da empresa: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar dados']);
    exit;
}

