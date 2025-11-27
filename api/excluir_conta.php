<?php
/**
 * API para excluir conta do usuário (PCD ou Empresa)
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../config/auth.php';

startSecureSession();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    exit;
}

// Verificar autenticação
if (!isAuthenticated()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Não autenticado']);
    exit;
}

$currentUser = getCurrentUser();
$userType = $_SESSION['user_type'] ?? '';

if (!$currentUser) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Usuário não encontrado']);
    exit;
}

try {
    $pdo = getDBConnection();
    if (!$pdo) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Erro de conexão com o banco de dados']);
        exit;
    }
    
    // Verificar se as tabelas existem antes de tentar excluir
    $checkTable = function($tableName) use ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
            $stmt->execute([$tableName]);
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            // Se houver erro, assumir que a tabela não existe
            return false;
        }
    };
    
    // Iniciar transação
    $pdo->beginTransaction();
    
    if ($userType === USER_TYPE_PCD) {
        // Excluir conta PCD
        $userId = intval($currentUser['id']);
        
        if (!$userId || $userId <= 0) {
            throw new Exception('ID de usuário inválido');
        }
        
        // Buscar arquivos para deletar
        $arquivosParaDeletar = [];
        
        if (!empty($currentUser['foto_perfil'])) {
            $arquivosParaDeletar[] = $currentUser['foto_perfil'];
        }
        if (!empty($currentUser['curriculo_path'])) {
            $arquivosParaDeletar[] = $currentUser['curriculo_path'];
        }
        if (!empty($currentUser['laudo_medico_path'])) {
            $arquivosParaDeletar[] = $currentUser['laudo_medico_path'];
        }
        
        // Excluir notificações relacionadas (se a tabela existir)
        if ($checkTable('notifications')) {
            $stmt = $pdo->prepare("DELETE FROM notifications WHERE user_id = ?");
            $stmt->execute([$userId]);
        }
        
        // Excluir histórico de status de candidaturas (se a tabela existir)
        if ($checkTable('application_status_history')) {
            // Primeiro buscar os IDs das applications
            $appStmt = $pdo->prepare("SELECT id FROM applications WHERE user_id = ?");
            $appStmt->execute([$userId]);
            $applicationIds = $appStmt->fetchAll(PDO::FETCH_COLUMN);
            
            if (!empty($applicationIds)) {
                $placeholders = implode(',', array_fill(0, count($applicationIds), '?'));
                $histStmt = $pdo->prepare("DELETE FROM application_status_history WHERE application_id IN ($placeholders)");
                $histStmt->execute($applicationIds);
            }
        }
        
        // As applications serão excluídas automaticamente pelo CASCADE
        // Excluir usuário (isso também excluirá applications pelo CASCADE)
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception('Usuário não encontrado ou já foi excluído');
        }
        
    } elseif ($userType === USER_TYPE_COMPANY) {
        // Excluir conta Empresa
        $companyId = intval($currentUser['id']);
        
        if (!$companyId || $companyId <= 0) {
            throw new Exception('ID de empresa inválido');
        }
        
        // Buscar arquivos para deletar
        $arquivosParaDeletar = [];
        
        if (!empty($currentUser['logo_path'])) {
            $arquivosParaDeletar[] = $currentUser['logo_path'];
        }
        if (!empty($currentUser['documento_empresa_path'])) {
            $arquivosParaDeletar[] = $currentUser['documento_empresa_path'];
        }
        
        // Buscar logos de vagas relacionadas (se houver)
        $jobsStmt = $pdo->prepare("SELECT id FROM jobs WHERE company_id = ?");
        $jobsStmt->execute([$companyId]);
        $jobs = $jobsStmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Excluir plan requests relacionados (se a tabela existir)
        if ($checkTable('plan_requests')) {
            $stmt = $pdo->prepare("DELETE FROM plan_requests WHERE company_id = ?");
            $stmt->execute([$companyId]);
        }
        
        // As jobs serão excluídas automaticamente pelo CASCADE
        // As applications relacionadas às jobs também serão excluídas pelo CASCADE
        // Excluir empresa (isso também excluirá jobs pelo CASCADE)
        $stmt = $pdo->prepare("DELETE FROM companies WHERE id = ?");
        $stmt->execute([$companyId]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception('Empresa não encontrada ou já foi excluída');
        }
        
    } else {
        $pdo->rollBack();
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Tipo de usuário inválido']);
        exit;
    }
    
    // Deletar arquivos físicos
    foreach ($arquivosParaDeletar as $arquivo) {
        if (!empty($arquivo)) {
            $filePath = strpos($arquivo, '/') === 0 ? substr($arquivo, 1) : $arquivo;
            $fullPath = __DIR__ . '/../' . $filePath;
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
        }
    }
    
    // Confirmar transação
    $pdo->commit();
    
    // Limpar sessão
    session_destroy();
    
    echo json_encode([
        'success' => true,
        'message' => 'Conta excluída com sucesso'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Erro ao excluir conta (PDO): " . $e->getMessage() . " | Código: " . $e->getCode());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => 'Erro ao excluir conta. Tente novamente mais tarde.',
        'debug' => (defined('DEBUG') && DEBUG) ? $e->getMessage() : null
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Erro ao excluir conta (Exception): " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage() ?: 'Erro ao excluir conta. Tente novamente mais tarde.'
    ], JSON_UNESCAPED_UNICODE);
}
?>

