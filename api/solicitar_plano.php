<?php
/**
 * API para Solicitar Plano
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../config/auth.php';

header('Content-Type: application/json; charset=utf-8');

startSecureSession();
requireAuth(USER_TYPE_COMPANY);

try {
    $pdo = getDBConnection();
    if (!$pdo) {
        throw new Exception("Erro ao conectar ao banco de dados.");
    }
    
    // Verificar se a tabela plan_requests existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'plan_requests'");
    if ($stmt->rowCount() === 0) {
        http_response_code(500);
        echo json_encode([
            'success' => false, 
            'error' => 'Tabela de planos não encontrada. Execute a migração migrate_planos.php primeiro.'
        ]);
        exit;
    }
    
    // Verificar método HTTP
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Método não permitido']);
        exit;
    }
    
    // Obter dados do usuário atual
    $currentUser = getCurrentUser();
    if (!$currentUser || !isset($currentUser['id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Usuário não autenticado']);
        exit;
    }
    
    $companyId = $currentUser['id'];
    
    // Obter dados do POST
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        $input = $_POST;
    }
    
    // Log para debug (remover em produção)
    error_log("Dados recebidos: " . print_r($input, true));
    
    $planoSolicitado = isset($input['plano']) ? strtolower(trim($input['plano'])) : '';
    $valor = isset($input['valor']) ? floatval($input['valor']) : 0;
    $observacoes = isset($input['observacoes']) ? trim($input['observacoes']) : '';
    
    // Validar plano
    $planosValidos = ['essencial', 'profissional', 'enterprise'];
    if (!in_array($planoSolicitado, $planosValidos)) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'error' => 'Plano inválido. Planos válidos: ' . implode(', ', $planosValidos),
            'plano_recebido' => $planoSolicitado
        ]);
        exit;
    }
    
    // Validar valor
    if ($valor <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'error' => 'Valor inválido',
            'valor_recebido' => $valor
        ]);
        exit;
    }
    
    // Verificar se já existe solicitação pendente para esta empresa
    try {
        $stmt = $pdo->prepare("
            SELECT id FROM plan_requests 
            WHERE company_id = ? AND status = 'pendente'
            LIMIT 1
        ");
        $stmt->execute([$companyId]);
        
        if ($stmt->rowCount() > 0) {
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'error' => 'Você já possui uma solicitação de plano pendente. Aguarde a aprovação.'
            ]);
            exit;
        }
    } catch (PDOException $e) {
        error_log("Erro ao verificar solicitação pendente: " . $e->getMessage());
        throw new Exception("Erro ao verificar solicitações pendentes: " . $e->getMessage());
    }
    
    // Buscar dados da empresa
    $stmt = $pdo->prepare("SELECT razao_social, nome_fantasia, plano FROM companies WHERE id = ?");
    $stmt->execute([$companyId]);
    $company = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$company) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Empresa não encontrada']);
        exit;
    }
    
    // Criar solicitação
    try {
        $stmt = $pdo->prepare("
            INSERT INTO plan_requests (company_id, plano_solicitado, valor, observacoes, status)
            VALUES (?, ?, ?, ?, 'pendente')
        ");
        
        $stmt->execute([
            $companyId,
            $planoSolicitado,
            $valor,
            $observacoes
        ]);
        
        $requestId = $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Erro ao inserir solicitação: " . $e->getMessage());
        throw new Exception("Erro ao criar solicitação: " . $e->getMessage());
    }
    
    // Atualizar status do plano da empresa para 'pendente' (se a coluna existir)
    try {
        // Verificar se a coluna existe antes de atualizar
        $stmt = $pdo->query("SHOW COLUMNS FROM companies LIKE 'plano_status'");
        if ($stmt->rowCount() > 0) {
            $stmt = $pdo->prepare("
                UPDATE companies 
                SET plano_status = 'pendente' 
                WHERE id = ?
            ");
            $stmt->execute([$companyId]);
        }
    } catch (PDOException $e) {
        // Não é crítico, apenas log
        error_log("Aviso: Não foi possível atualizar plano_status: " . $e->getMessage());
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Solicitação de plano enviada com sucesso! Aguarde a aprovação do administrador.',
        'data' => [
            'id' => $requestId,
            'plano' => $planoSolicitado,
            'valor' => $valor,
            'status' => 'pendente'
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Erro ao solicitar plano (PDO): " . $e->getMessage());
    error_log("Código SQL: " . $e->getCode());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => 'Erro interno ao processar solicitação',
        'details' => $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Erro ao solicitar plano (Exception): " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
}
exit;
?>

