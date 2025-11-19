<?php
/**
 * API Admin - Gestão de Usuários PCD
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../config/auth.php';

startSecureSession();
requireAuth(USER_TYPE_ADMIN);

header('Content-Type: application/json');

$pdo = getDBConnection();
if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro de conexão']);
    exit;
}

$action = $_GET['action'] ?? 'list';

try {
    switch ($action) {
        case 'list':
            $status = $_GET['status'] ?? 'todas';
            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $limit = isset($_GET['limit']) ? min(100, max(1, (int)$_GET['limit'])) : 20;
            $offset = ($page - 1) * $limit;
            
            $where = ["tipo = 'pcd'"];
            $params = [];
            
            if ($status !== 'todas') {
                $where[] = "status = :status";
                $params[':status'] = $status;
            }
            
            $whereClause = implode(' AND ', $where);
            
            // Contar total
            $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM users WHERE $whereClause");
            foreach ($params as $key => $value) {
                $countStmt->bindValue($key, $value);
            }
            $countStmt->execute();
            $total = $countStmt->fetch()['total'];
            
            // Buscar usuários
            $stmt = $pdo->prepare("
                SELECT id, nome, email, cpf, telefone, tipo_deficiencia, status, created_at
                FROM users
                WHERE $whereClause
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset
            ");
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            echo json_encode([
                'success' => true,
                'data' => $stmt->fetchAll(),
                'pagination' => [
                    'page' => $page,
                    'limit' => $limit,
                    'total' => $total,
                    'pages' => ceil($total / $limit)
                ]
            ], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'update_status':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['error' => 'Método não permitido']);
                exit;
            }
            
            $user_id = (int)$_POST['user_id'];
            $new_status = $_POST['status'];
            
            if (!in_array($new_status, ['ativo', 'inativo', 'pendente'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Status inválido']);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE users SET status = :status WHERE id = :id AND tipo = 'pcd'");
            $stmt->execute([':status' => $new_status, ':id' => $user_id]);
            
            echo json_encode(['success' => true, 'message' => 'Status atualizado']);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Ação inválida']);
    }
    
} catch (PDOException $e) {
    error_log("Erro na API admin: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao processar requisição']);
    exit;
}

