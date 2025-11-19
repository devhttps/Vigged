<?php
/**
 * API para Gerenciar Candidaturas (aprovar, rejeitar, cancelar)
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../config/auth.php';

startSecureSession();

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$application_id = isset($_POST['application_id']) ? (int)$_POST['application_id'] : (isset($_GET['application_id']) ? (int)$_GET['application_id'] : 0);

if ($application_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID da candidatura inválido']);
    exit;
}

$pdo = getDBConnection();
if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro de conexão']);
    exit;
}

try {
    // Buscar candidatura
    $stmt = $pdo->prepare("
        SELECT a.*, j.company_id, j.titulo as vaga_titulo
        FROM applications a
        INNER JOIN jobs j ON a.job_id = j.id
        WHERE a.id = ?
    ");
    $stmt->execute([$application_id]);
    $application = $stmt->fetch();
    
    if (!$application) {
        http_response_code(404);
        echo json_encode(['error' => 'Candidatura não encontrada']);
        exit;
    }
    
    $currentUser = getCurrentUser();
    
    // Verificar permissões
    if ($action === 'cancelar') {
        // Candidato pode cancelar sua própria candidatura
        requireAuth(USER_TYPE_PCD);
        if ($currentUser['user_id'] != $application['user_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'Sem permissão']);
            exit;
        }
    } else {
        // Empresa pode aprovar/rejeitar candidaturas de suas vagas
        requireAuth(USER_TYPE_COMPANY);
        if ($currentUser['user_id'] != $application['company_id']) {
            http_response_code(403);
            echo json_encode(['error' => 'Sem permissão']);
            exit;
        }
    }
    
    switch ($action) {
        case 'aprovar':
            $stmt = $pdo->prepare("UPDATE applications SET status = 'aprovada' WHERE id = ?");
            $stmt->execute([$application_id]);
            echo json_encode(['success' => true, 'message' => 'Candidatura aprovada com sucesso']);
            break;
            
        case 'rejeitar':
            $mensagem = $_POST['mensagem'] ?? '';
            $stmt = $pdo->prepare("UPDATE applications SET status = 'rejeitada', mensagem = ? WHERE id = ?");
            $stmt->execute([$mensagem, $application_id]);
            echo json_encode(['success' => true, 'message' => 'Candidatura rejeitada']);
            break;
            
        case 'cancelar':
            $stmt = $pdo->prepare("UPDATE applications SET status = 'cancelada' WHERE id = ?");
            $stmt->execute([$application_id]);
            echo json_encode(['success' => true, 'message' => 'Candidatura cancelada com sucesso']);
            break;
            
        case 'em_analise':
            $stmt = $pdo->prepare("UPDATE applications SET status = 'em_analise' WHERE id = ?");
            $stmt->execute([$application_id]);
            echo json_encode(['success' => true, 'message' => 'Status atualizado para em análise']);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Ação inválida']);
            exit;
    }
    
} catch (PDOException $e) {
    error_log("Erro ao gerenciar candidatura: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao processar requisição']);
    exit;
}

