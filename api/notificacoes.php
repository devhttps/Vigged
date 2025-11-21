<?php
/**
 * API para Gerenciar Notificações
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

startSecureSession();
requireAuth();

header('Content-Type: application/json');

$action = $_GET['action'] ?? $_POST['action'] ?? 'listar';
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
    switch ($action) {
        case 'listar':
            $lida = isset($_GET['lida']) ? (int)$_GET['lida'] : null;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
            
            $where = ["user_id = :user_id"];
            $params = [':user_id' => $user_id];
            
            if ($lida !== null) {
                $where[] = "lida = :lida";
                $params[':lida'] = $lida;
            }
            
            $whereClause = implode(' AND ', $where);
            
            $stmt = $pdo->prepare("
                SELECT * FROM notifications
                WHERE $whereClause
                ORDER BY created_at DESC
                LIMIT :limit
            ");
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            $notificacoes = $stmt->fetchAll();
            
            // Contar não lidas
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM notifications WHERE user_id = ? AND lida = 0");
            $stmt->execute([$user_id]);
            $nao_lidas = $stmt->fetch()['total'];
            
            echo json_encode([
                'success' => true,
                'data' => $notificacoes,
                'nao_lidas' => (int)$nao_lidas,
                'total' => count($notificacoes)
            ], JSON_UNESCAPED_UNICODE);
            break;
            
        case 'marcar_lida':
            $notification_id = isset($_POST['notification_id']) ? (int)$_POST['notification_id'] : 0;
            
            if ($notification_id <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'ID da notificação inválido']);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE notifications SET lida = 1 WHERE id = ? AND user_id = ?");
            $stmt->execute([$notification_id, $user_id]);
            
            if ($stmt->rowCount() > 0) {
                echo json_encode(['success' => true, 'message' => 'Notificação marcada como lida']);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Notificação não encontrada']);
            }
            break;
            
        case 'marcar_todas_lidas':
            $stmt = $pdo->prepare("UPDATE notifications SET lida = 1 WHERE user_id = ? AND lida = 0");
            $stmt->execute([$user_id]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Todas as notificações foram marcadas como lidas',
                'marcadas' => $stmt->rowCount()
            ]);
            break;
            
        case 'contar_nao_lidas':
            $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM notifications WHERE user_id = ? AND lida = 0");
            $stmt->execute([$user_id]);
            $total = $stmt->fetch()['total'];
            
            echo json_encode([
                'success' => true,
                'nao_lidas' => (int)$total
            ]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Ação inválida']);
            exit;
    }
    
} catch (PDOException $e) {
    error_log("Erro ao gerenciar notificações: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao processar requisição']);
    exit;
}

