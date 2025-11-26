<?php
/**
 * API para Aprovar/Rejeitar Planos
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../config/auth.php';

header('Content-Type: application/json; charset=utf-8');

startSecureSession();
requireAdmin();

try {
    $pdo = getDBConnection();
    if (!$pdo) {
        throw new Exception("Erro ao conectar ao banco de dados.");
    }
    
    // Verificar método HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Método não permitido']);
        exit;
    }
    
    // Obter dados do administrador atual
    $currentUser = getCurrentUser();
    $adminId = $currentUser['id'];
    
    // Obter dados do POST
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }
    
    $requestId = isset($input['request_id']) ? intval($input['request_id']) : 0;
    $acao = isset($input['acao']) ? strtolower(trim($input['acao'])) : '';
    $motivoRejeicao = isset($input['motivo_rejeicao']) ? trim($input['motivo_rejeicao']) : '';
    
    // Validar ação
    if (!in_array($acao, ['aprovar', 'rejeitar'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Ação inválida']);
        exit;
    }
    
    // Buscar solicitação
    $stmt = $pdo->prepare("
        SELECT pr.*, c.razao_social, c.nome_fantasia
        FROM plan_requests pr
        INNER JOIN companies c ON pr.company_id = c.id
        WHERE pr.id = ?
    ");
    $stmt->execute([$requestId]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$request) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Solicitação não encontrada']);
        exit;
    }
    
    // Verificar se já foi processada
    if ($request['status'] !== 'pendente') {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'error' => 'Esta solicitação já foi processada'
        ]);
        exit;
    }
    
    // Iniciar transação
    $pdo->beginTransaction();
    
    try {
        if ($acao === 'aprovar') {
            // Atualizar solicitação
            $stmt = $pdo->prepare("
                UPDATE plan_requests 
                SET status = 'aprovado',
                    aprovado_por = ?,
                    aprovado_em = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$adminId, $requestId]);
            
            // Atualizar plano da empresa
            $stmt = $pdo->prepare("
                UPDATE companies 
                SET plano = ?,
                    plano_status = 'ativo'
                WHERE id = ?
            ");
            $stmt->execute([
                $request['plano_solicitado'],
                $request['company_id']
            ]);
            
            $message = "Plano aprovado com sucesso!";
            
        } else { // rejeitar
            // Validar motivo de rejeição
            if (empty($motivoRejeicao)) {
                $pdo->rollBack();
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'error' => 'Motivo da rejeição é obrigatório'
                ]);
                exit;
            }
            
            // Atualizar solicitação
            $stmt = $pdo->prepare("
                UPDATE plan_requests 
                SET status = 'rejeitado',
                    motivo_rejeicao = ?,
                    aprovado_por = ?,
                    aprovado_em = NOW()
                WHERE id = ?
            ");
            $stmt->execute([
                $motivoRejeicao,
                $adminId,
                $requestId
            ]);
            
            // Atualizar status do plano da empresa
            $stmt = $pdo->prepare("
                UPDATE companies 
                SET plano_status = 'ativo'
                WHERE id = ? AND plano_status = 'pendente'
            ");
            $stmt->execute([$request['company_id']]);
            
            $message = "Plano rejeitado com sucesso!";
        }
        
        // Commit transação
        $pdo->commit();
        
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => [
                'request_id' => $requestId,
                'acao' => $acao,
                'empresa' => $request['nome_fantasia'] ?: $request['razao_social']
            ]
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
    
} catch (PDOException $e) {
    error_log("Erro ao gerenciar plano: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro interno ao processar solicitação']);
} catch (Exception $e) {
    error_log("Erro ao gerenciar plano: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
exit;
?>

