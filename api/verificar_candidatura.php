<?php
/**
 * API para Verificar se Usuário PCD já se candidatou a uma vaga
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

startSecureSession();
requireAuth(USER_TYPE_PCD);

header('Content-Type: application/json');

$job_id = isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0;

if ($job_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID da vaga inválido']);
    exit;
}

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
    $stmt = $pdo->prepare("
        SELECT 
            id,
            status,
            created_at,
            updated_at,
            mensagem,
            feedback,
            avaliacao
        FROM applications 
        WHERE user_id = ? AND job_id = ?
        LIMIT 1
    ");
    $stmt->execute([$user_id, $job_id]);
    $application = $stmt->fetch();
    
    if ($application) {
        $status_labels = [
            'pendente' => 'Pendente',
            'em_analise' => 'Em Análise',
            'aprovada' => 'Aprovada',
            'rejeitada' => 'Reprovada',
            'cancelada' => 'Cancelada'
        ];
        
        echo json_encode([
            'success' => true,
            'ja_candidatou' => true,
            'candidatura' => [
                'id' => $application['id'],
                'status' => $application['status'],
                'status_label' => $status_labels[$application['status']] ?? $application['status'],
                'created_at' => $application['created_at'],
                'updated_at' => $application['updated_at'],
                'mensagem' => $application['mensagem'],
                'feedback' => $application['feedback'],
                'avaliacao' => $application['avaliacao']
            ]
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => true,
            'ja_candidatou' => false
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch (PDOException $e) {
    error_log("Erro ao verificar candidatura: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao verificar candidatura']);
    exit;
}

