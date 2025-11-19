<?php
/**
 * API para Gerenciar Vagas (pausar, encerrar, deletar)
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

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$job_id = isset($_POST['job_id']) ? (int)$_POST['job_id'] : (isset($_GET['job_id']) ? (int)$_GET['job_id'] : 0);

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
    $stmt = $pdo->prepare("SELECT id, status FROM jobs WHERE id = ? AND company_id = ?");
    $stmt->execute([$job_id, $company_id]);
    $job = $stmt->fetch();
    
    if (!$job) {
        http_response_code(404);
        echo json_encode(['error' => 'Vaga não encontrada ou sem permissão']);
        exit;
    }
    
    switch ($action) {
        case 'pausar':
            $stmt = $pdo->prepare("UPDATE jobs SET status = 'pausada' WHERE id = ? AND company_id = ?");
            $stmt->execute([$job_id, $company_id]);
            echo json_encode(['success' => true, 'message' => 'Vaga pausada com sucesso']);
            break;
            
        case 'ativar':
            $stmt = $pdo->prepare("UPDATE jobs SET status = 'ativa' WHERE id = ? AND company_id = ?");
            $stmt->execute([$job_id, $company_id]);
            echo json_encode(['success' => true, 'message' => 'Vaga ativada com sucesso']);
            break;
            
        case 'encerrar':
            $stmt = $pdo->prepare("UPDATE jobs SET status = 'encerrada' WHERE id = ? AND company_id = ?");
            $stmt->execute([$job_id, $company_id]);
            echo json_encode(['success' => true, 'message' => 'Vaga encerrada com sucesso']);
            break;
            
        case 'deletar':
            // Verificar se há candidaturas
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM applications WHERE job_id = ?");
            $stmt->execute([$job_id]);
            $result = $stmt->fetch();
            
            if ($result['total'] > 0) {
                http_response_code(400);
                echo json_encode(['error' => 'Não é possível deletar vaga com candidaturas. Encerre a vaga ao invés de deletar.']);
                exit;
            }
            
            $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ? AND company_id = ?");
            $stmt->execute([$job_id, $company_id]);
            echo json_encode(['success' => true, 'message' => 'Vaga deletada com sucesso']);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Ação inválida']);
            exit;
    }
    
} catch (PDOException $e) {
    error_log("Erro ao gerenciar vaga: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao processar requisição']);
    exit;
}

