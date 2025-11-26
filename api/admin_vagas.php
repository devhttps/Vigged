<?php
/**
 * API para Gerenciar Vagas (Admin)
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
        // Listar todas as vagas
        $status = $_GET['status'] ?? 'todas';
        $search = $_GET['search'] ?? '';
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? min(50, max(1, (int)$_GET['limit'])) : 10;
        $offset = ($page - 1) * $limit;
        
        // Construir query
        $where = [];
        $params = [];
        
        if ($status !== 'todas') {
            $where[] = "j.status = :status";
            $params[':status'] = $status;
        }
        
        if (!empty($search)) {
            $where[] = "(j.titulo LIKE :search OR j.descricao LIKE :search OR c.razao_social LIKE :search OR c.nome_fantasia LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        
        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        // Contar total
        $countQuery = "
            SELECT COUNT(*) as total
            FROM jobs j
            INNER JOIN companies c ON j.company_id = c.id
            $whereClause
        ";
        
        $countStmt = $pdo->prepare($countQuery);
        foreach ($params as $key => $value) {
            $countStmt->bindValue($key, $value);
        }
        $countStmt->execute();
        $total = $countStmt->fetch()['total'];
        
        // Buscar vagas
        $query = "
            SELECT 
                j.*,
                c.id as company_id,
                c.razao_social,
                c.nome_fantasia,
                c.email_corporativo,
                COUNT(a.id) as total_candidaturas
            FROM jobs j
            INNER JOIN companies c ON j.company_id = c.id
            LEFT JOIN applications a ON j.id = a.job_id
            $whereClause
            GROUP BY j.id
            ORDER BY j.created_at DESC
            LIMIT :limit OFFSET :offset
        ";
        
        $stmt = $pdo->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        $vagas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'data' => $vagas,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ], JSON_UNESCAPED_UNICODE);
        
    } elseif ($action === 'update_status') {
        // Atualizar status da vaga
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
        
        $jobId = isset($input['job_id']) ? intval($input['job_id']) : 0;
        $status = isset($input['status']) ? trim($input['status']) : '';
        
        if (!$jobId || !in_array($status, ['ativa', 'pausada', 'encerrada'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Parâmetros inválidos']);
            exit;
        }
        
        $stmt = $pdo->prepare("UPDATE jobs SET status = ? WHERE id = ?");
        $stmt->execute([$status, $jobId]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Status da vaga atualizado com sucesso'
        ]);
        
    } elseif ($action === 'delete') {
        // Deletar vaga
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
        
        $jobId = isset($input['job_id']) ? intval($input['job_id']) : 0;
        
        if (!$jobId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'ID da vaga inválido']);
            exit;
        }
        
        $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
        $stmt->execute([$jobId]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Vaga deletada com sucesso'
        ]);
        
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Ação inválida']);
    }
    
} catch (PDOException $e) {
    error_log("Erro ao gerenciar vagas (admin): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Erro interno ao processar solicitação']);
} catch (Exception $e) {
    error_log("Erro ao gerenciar vagas (admin): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
exit;
?>

