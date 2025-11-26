<?php
/**
 * API para Gerenciar Planos das Empresas (Admin)
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
    
    $action = $_GET['action'] ?? 'list';
    
    if ($action === 'list') {
        // Listar empresas com seus planos
        $plano = $_GET['plano'] ?? 'todas';
        $status = $_GET['status'] ?? 'todas';
        $search = $_GET['search'] ?? '';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? min(50, max(1, (int)$_GET['limit'])) : 10;
        $offset = ($page - 1) * $limit;
        
        // Construir query
        $where = [];
        $params = [];
        
        if ($plano !== 'todas') {
            $where[] = "c.plano = :plano";
            $params[':plano'] = $plano;
        }
        
        if ($status !== 'todas') {
            if ($status === 'com_plano') {
                $where[] = "c.plano != 'gratuito'";
            } else {
                $where[] = "c.plano_status = :plano_status";
                $params[':plano_status'] = $status;
            }
        }
        
        if (!empty($search)) {
            $where[] = "(c.razao_social LIKE :search OR c.nome_fantasia LIKE :search OR c.cnpj LIKE :search OR c.email_corporativo LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        // Contar total
        $countQuery = "
            SELECT COUNT(*) as total
            FROM companies c
            $whereClause
        ";
        
        $countStmt = $pdo->prepare($countQuery);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = $countStmt->fetch()['total'];
        
        // Buscar empresas com planos
        $query = "
            SELECT 
                c.*,
                COUNT(DISTINCT j.id) as total_vagas,
                COUNT(DISTINCT CASE WHEN j.status = 'ativa' THEN j.id END) as vagas_ativas,
                COUNT(DISTINCT a.id) as total_candidaturas,
                (SELECT COUNT(*) FROM plan_requests pr WHERE pr.company_id = c.id AND pr.status = 'pendente') as solicitacoes_pendentes
            FROM companies c
            LEFT JOIN jobs j ON c.id = j.company_id
            LEFT JOIN applications a ON j.id = a.job_id
            $whereClause
            GROUP BY c.id
            ORDER BY c.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $stmt = $pdo->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $empresas,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ], JSON_UNESCAPED_UNICODE);
        
    } elseif ($action === 'update_plano') {
        // Atualizar plano da empresa
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
        
        $companyId = isset($input['company_id']) ? intval($input['company_id']) : 0;
        $novoPlano = isset($input['plano']) ? strtolower(trim($input['plano'])) : '';
        $planoStatus = isset($input['plano_status']) ? trim($input['plano_status']) : 'ativo';
        
        $planosValidos = ['gratuito', 'essencial', 'profissional', 'enterprise'];
        $statusValidos = ['ativo', 'pendente', 'cancelado', 'expirado'];
        
        if (!$companyId || !in_array($novoPlano, $planosValidos) || !in_array($planoStatus, $statusValidos)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Parâmetros inválidos']);
            exit;
        }
        
        // Verificar se empresa existe
        $stmt = $pdo->prepare("SELECT id, razao_social, nome_fantasia, plano FROM companies WHERE id = ?");
        $stmt->execute([$companyId]);
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$empresa) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Empresa não encontrada']);
            exit;
        }
        
        $planoAnterior = $empresa['plano'];
        
        // Iniciar transação
        $pdo->beginTransaction();
        
        try {
            // Atualizar plano da empresa
            $stmt = $pdo->prepare("UPDATE companies SET plano = ?, plano_status = ? WHERE id = ?");
            $stmt->execute([$novoPlano, $planoStatus, $companyId]);
            
            // Se estava mudando de um plano pago para gratuito, cancelar solicitações pendentes
            if ($planoAnterior !== 'gratuito' && $novoPlano === 'gratuito') {
                $stmt = $pdo->prepare("
                    UPDATE plan_requests 
                    SET status = 'rejeitado', 
                        motivo_rejeicao = 'Plano removido pelo administrador',
                        aprovado_por = ?,
                        aprovado_em = NOW()
                    WHERE company_id = ? AND status = 'pendente'
                ");
                $currentUser = getCurrentUser();
                $adminId = $currentUser['id'] ?? null;
                $stmt->execute([$adminId, $companyId]);
            }
            
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Plano atualizado com sucesso',
                'data' => [
                    'empresa' => $empresa['nome_fantasia'] ?: $empresa['razao_social'],
                    'plano_anterior' => $planoAnterior,
                    'plano_novo' => $novoPlano,
                    'status' => $planoStatus
                ]
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
        
    } elseif ($action === 'remove_plano') {
        // Remover plano (voltar para gratuito)
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
        
        $companyId = isset($input['company_id']) ? intval($input['company_id']) : 0;
        
        if (!$companyId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID da empresa inválido']);
            exit;
        }
        
        // Verificar se empresa existe
        $stmt = $pdo->prepare("SELECT id, razao_social, nome_fantasia, plano FROM companies WHERE id = ?");
        $stmt->execute([$companyId]);
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$empresa) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Empresa não encontrada']);
            exit;
        }
        
        if ($empresa['plano'] === 'gratuito') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Empresa já possui plano gratuito']);
            exit;
        }
        
        // Iniciar transação
        $pdo->beginTransaction();
        
        try {
            // Remover plano (voltar para gratuito)
            $stmt = $pdo->prepare("UPDATE companies SET plano = 'gratuito', plano_status = 'ativo' WHERE id = ?");
            $stmt->execute([$companyId]);
            
            // Cancelar solicitações pendentes
            $stmt = $pdo->prepare("
                UPDATE plan_requests 
                SET status = 'rejeitado', 
                    motivo_rejeicao = 'Plano removido pelo administrador',
                    aprovado_por = ?,
                    aprovado_em = NOW()
                WHERE company_id = ? AND status = 'pendente'
            ");
            $currentUser = getCurrentUser();
            $adminId = $currentUser['id'] ?? null;
            $stmt->execute([$adminId, $companyId]);
            
            $pdo->commit();
            
            echo json_encode([
                'success' => true,
                'message' => 'Plano removido com sucesso. Empresa voltou para o plano gratuito.',
                'data' => [
                    'empresa' => $empresa['nome_fantasia'] ?: $empresa['razao_social'],
                    'plano_anterior' => $empresa['plano'],
                    'plano_novo' => 'gratuito'
                ]
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
        
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Ação inválida']);
    }
    
} catch (PDOException $e) {
    error_log("Erro ao gerenciar planos (admin): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro interno ao processar solicitação']);
} catch (Exception $e) {
    error_log("Erro ao gerenciar planos (admin): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
exit;
?>

