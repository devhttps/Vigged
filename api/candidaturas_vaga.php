<?php
/**
 * API para Listar Candidaturas de uma Vaga (Empresa)
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../config/auth.php';

startSecureSession();
requireAuth(USER_TYPE_COMPANY);

header('Content-Type: application/json');

$currentUser = getCurrentUser();
$company_id = $currentUser['user_id'] ?? null;

if (!$company_id) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autenticado']);
    exit;
}

$job_id = isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0;

if ($job_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID da vaga inválido']);
    exit;
}

$pdo = getDBConnection();
if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro de conexão']);
    exit;
}

try {
    // Verificar se a vaga pertence à empresa
    $stmt = $pdo->prepare("SELECT id FROM jobs WHERE id = ? AND company_id = ?");
    $stmt->execute([$job_id, $company_id]);
    $job = $stmt->fetch();
    
    if (!$job) {
        http_response_code(404);
        echo json_encode(['error' => 'Vaga não encontrada ou sem permissão']);
        exit;
    }
    
    // Buscar candidaturas
    $status = $_GET['status'] ?? 'todas';
    $where = ["a.job_id = :job_id"];
    $params = [':job_id' => $job_id];
    
    if ($status !== 'todas') {
        $where[] = "a.status = :status";
        $params[':status'] = $status;
    }
    
    $whereClause = implode(' AND ', $where);
    
    $stmt = $pdo->prepare("
        SELECT 
            a.*,
            u.nome as candidato_nome,
            u.email as candidato_email,
            u.telefone as candidato_telefone,
            u.tipo_deficiencia,
            u.cid
        FROM applications a
        INNER JOIN users u ON a.user_id = u.id
        WHERE $whereClause
        ORDER BY a.created_at DESC
    ");
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    $candidaturas = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'data' => $candidaturas,
        'total' => count($candidaturas)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar candidaturas: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar candidaturas']);
    exit;
}

