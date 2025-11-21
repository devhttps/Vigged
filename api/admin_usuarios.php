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
            
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['error' => 'Método não permitido']);
                exit;
            }
            
            require_once '../includes/functions.php';
            
            $nome = sanitizeInput($_POST['nome'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $cpf = sanitizeInput($_POST['cpf'] ?? '');
            $telefone = sanitizeInput($_POST['telefone'] ?? '');
            $tipo_deficiencia = sanitizeInput($_POST['tipo_deficiencia'] ?? '');
            $status = sanitizeInput($_POST['status'] ?? 'ativo');
            $senha = $_POST['senha'] ?? '';
            
            // Validações
            if (empty($nome) || empty($email) || empty($cpf) || empty($senha)) {
                http_response_code(400);
                echo json_encode(['error' => 'Campos obrigatórios: nome, email, CPF e senha']);
                exit;
            }
            
            if (!validateEmail($email)) {
                http_response_code(400);
                echo json_encode(['error' => 'Email inválido']);
                exit;
            }
            
            if (!validateCPF($cpf)) {
                http_response_code(400);
                echo json_encode(['error' => 'CPF inválido']);
                exit;
            }
            
            if (strlen($senha) < 6) {
                http_response_code(400);
                echo json_encode(['error' => 'Senha deve ter no mínimo 6 caracteres']);
                exit;
            }
            
            // Verificar se email ou CPF já existe
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = :email OR cpf = :cpf");
            $checkStmt->execute([':email' => $email, ':cpf' => $cpf]);
            if ($checkStmt->fetch()) {
                http_response_code(400);
                echo json_encode(['error' => 'Email ou CPF já cadastrado']);
                exit;
            }
            
            // Criar usuário
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO users (nome, email, cpf, telefone, tipo_deficiencia, senha, tipo, status, created_at)
                VALUES (:nome, :email, :cpf, :telefone, :tipo_deficiencia, :senha, 'pcd', :status, NOW())
            ");
            
            $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':cpf' => $cpf,
                ':telefone' => $telefone,
                ':tipo_deficiencia' => $tipo_deficiencia,
                ':senha' => $senhaHash,
                ':status' => $status
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Usuário criado com sucesso', 'id' => $pdo->lastInsertId()]);
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

