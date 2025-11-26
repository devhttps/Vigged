<?php
/**
 * API para Listar Planos Pendentes
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
    
    // Buscar solicitações pendentes com dados da empresa
    $stmt = $pdo->query("
        SELECT 
            pr.id,
            pr.company_id,
            pr.plano_solicitado,
            pr.valor,
            pr.observacoes,
            pr.status,
            pr.created_at,
            pr.updated_at,
            c.razao_social,
            c.nome_fantasia,
            c.cnpj,
            c.email_corporativo,
            c.plano as plano_atual,
            c.plano_status
        FROM plan_requests pr
        INNER JOIN companies c ON pr.company_id = c.id
        WHERE pr.status = 'pendente'
        ORDER BY pr.created_at DESC
    ");
    
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Formatar dados
    foreach ($requests as &$request) {
        $request['plano_nome'] = ucfirst($request['plano_solicitado']);
        $request['created_at_formatted'] = date('d/m/Y H:i', strtotime($request['created_at']));
    }
    
    echo json_encode([
        'success' => true,
        'data' => $requests,
        'total' => count($requests)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Erro ao listar planos pendentes: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro interno ao listar solicitações']);
} catch (Exception $e) {
    error_log("Erro ao listar planos pendentes: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
exit;
?>

