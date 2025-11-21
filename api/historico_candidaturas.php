<?php
/**
 * API para Listar Histórico de Candidaturas do Usuário PCD
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

startSecureSession();
requireAuth(USER_TYPE_PCD);

header('Content-Type: application/json');

$currentUser = getCurrentUser();
$user_id = $currentUser['id'] ?? null;

if (!$user_id) {
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
    $status = $_GET['status'] ?? null;
    
    $where = ["a.user_id = :user_id"];
    $params = [':user_id' => $user_id];
    
    if ($status && $status !== 'todas') {
        $where[] = "a.status = :status";
        $params[':status'] = $status;
    }
    
    $whereClause = implode(' AND ', $where);
    
    $stmt = $pdo->prepare("
        SELECT 
            a.*,
            j.titulo as vaga_titulo,
            j.descricao as vaga_descricao,
            j.localizacao,
            j.tipo_contrato,
            j.faixa_salarial,
            c.razao_social as empresa_nome,
            c.nome_fantasia as empresa_fantasia,
            c.logo_path as empresa_logo
        FROM applications a
        INNER JOIN jobs j ON a.job_id = j.id
        INNER JOIN companies c ON j.company_id = c.id
        WHERE $whereClause
        ORDER BY a.created_at DESC
    ");
    
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    $stmt->execute();
    $candidaturas = $stmt->fetchAll();
    
    // Formatar dados
    $status_labels = [
        'pendente' => 'Pendente',
        'em_analise' => 'Em Análise',
        'aprovada' => 'Aprovada',
        'rejeitada' => 'Reprovada',
        'cancelada' => 'Cancelada'
    ];
    
    foreach ($candidaturas as &$candidatura) {
        $candidatura['status_label'] = $status_labels[$candidatura['status']] ?? $candidatura['status'];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $candidaturas,
        'total' => count($candidaturas)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar histórico: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar histórico']);
    exit;
}

