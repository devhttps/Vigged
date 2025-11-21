<?php
/**
 * API Admin - Gestão de Empresas
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
            
            $where = [];
            $params = [];
            
            if ($status !== 'todas') {
                $where[] = "status = :status";
                $params[':status'] = $status;
            }
            
            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
            
            // Contar total
            $countStmt = $pdo->prepare("SELECT COUNT(*) as total FROM companies $whereClause");
            foreach ($params as $key => $value) {
                $countStmt->bindValue($key, $value);
            }
            $countStmt->execute();
            $total = $countStmt->fetch()['total'];
            
            // Buscar empresas
            $stmt = $pdo->prepare("
                SELECT 
                    c.*,
                    COUNT(DISTINCT j.id) as total_vagas,
                    COUNT(DISTINCT CASE WHEN j.status = 'ativa' THEN j.id END) as vagas_ativas
                FROM companies c
                LEFT JOIN jobs j ON c.id = j.company_id
                $whereClause
                GROUP BY c.id
                ORDER BY c.created_at DESC
                LIMIT :limit OFFSET :offset
            ");
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $empresas = $stmt->fetchAll();
            foreach ($empresas as &$empresa) {
                unset($empresa['senha']);
            }
            
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
            break;
            
        case 'update_status':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['error' => 'Método não permitido']);
                exit;
            }
            
            $company_id = (int)$_POST['company_id'];
            $new_status = $_POST['status'];
            
            if (!in_array($new_status, ['ativa', 'inativa', 'pendente'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Status inválido']);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE companies SET status = :status WHERE id = :id");
            $stmt->execute([':status' => $new_status, ':id' => $company_id]);
            
            echo json_encode(['success' => true, 'message' => 'Status atualizado']);
            break;
            
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Método não permitido']);
                exit;
            }
            
            require_once '../includes/functions.php';
            
            // Receber dados do POST
            $razao_social = trim($_POST['razao_social'] ?? '');
            $nome_fantasia = trim($_POST['nome_fantasia'] ?? '');
            $cnpj = trim($_POST['cnpj'] ?? '');
            $email_corporativo = trim($_POST['email_corporativo'] ?? '');
            $telefone = trim($_POST['telefone'] ?? '');
            $setor = trim($_POST['setor'] ?? '');
            $status = trim($_POST['status'] ?? 'ativa');
            $senha = $_POST['senha'] ?? '';
            
            // Validações básicas
            if (empty($razao_social)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Razão social é obrigatória']);
                exit;
            }
            
            if (empty($cnpj)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'CNPJ é obrigatório']);
                exit;
            }
            
            if (empty($email_corporativo)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Email corporativo é obrigatório']);
                exit;
            }
            
            if (empty($senha)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Senha é obrigatória']);
                exit;
            }
            
            // Sanitizar dados
            $razao_social = sanitizeInput($razao_social);
            $nome_fantasia = sanitizeInput($nome_fantasia);
            $cnpj_limpo = preg_replace('/[^0-9]/', '', $cnpj);
            $email_corporativo = sanitizeInput($email_corporativo);
            $telefone = sanitizeInput($telefone);
            $setor = sanitizeInput($setor);
            $status = sanitizeInput($status);
            
            // Validações de formato
            if (!validateEmail($email_corporativo)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Email inválido']);
                exit;
            }
            
            if (!validateCNPJ($cnpj_limpo)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'CNPJ inválido. Deve conter 14 dígitos']);
                exit;
            }
            
            if (strlen($senha) < 6) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Senha deve ter no mínimo 6 caracteres']);
                exit;
            }
            
            // Verificar se email ou CNPJ já existe
            try {
                $checkStmt = $pdo->prepare("SELECT id FROM companies WHERE email_corporativo = :email OR cnpj = :cnpj");
                $checkStmt->execute([':email' => $email_corporativo, ':cnpj' => $cnpj_limpo]);
                if ($checkStmt->fetch()) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Email ou CNPJ já cadastrado']);
                    exit;
                }
            } catch (PDOException $e) {
                error_log("Erro ao verificar duplicatas: " . $e->getMessage());
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => 'Erro ao verificar dados existentes']);
                exit;
            }
            
            // Criar empresa
            try {
                $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    INSERT INTO companies (razao_social, nome_fantasia, cnpj, email_corporativo, telefone_empresa, setor, senha, status, created_at)
                    VALUES (:razao_social, :nome_fantasia, :cnpj, :email_corporativo, :telefone_empresa, :setor, :senha, :status, NOW())
                ");
                
                $result = $stmt->execute([
                    ':razao_social' => $razao_social,
                    ':nome_fantasia' => $nome_fantasia ?: $razao_social,
                    ':cnpj' => $cnpj_limpo,
                    ':email_corporativo' => $email_corporativo,
                    ':telefone_empresa' => $telefone,
                    ':setor' => $setor,
                    ':senha' => $senhaHash,
                    ':status' => $status
                ]);
                
                if ($result) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Empresa criada com sucesso', 
                        'id' => $pdo->lastInsertId()
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'error' => 'Erro ao inserir empresa no banco de dados']);
                }
            } catch (PDOException $e) {
                error_log("Erro ao criar empresa: " . $e->getMessage());
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => 'Erro ao criar empresa',
                    'details' => $e->getMessage() // Mostrar detalhes em desenvolvimento
                ]);
                exit;
            }
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Ação inválida']);
    }
    
} catch (PDOException $e) {
    error_log("Erro na API admin empresas: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao processar requisição',
        'details' => $e->getMessage() // Em desenvolvimento, mostrar detalhes
    ]);
    exit;
} catch (Exception $e) {
    error_log("Erro na API admin empresas: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao processar requisição',
        'details' => $e->getMessage() // Em desenvolvimento, mostrar detalhes
    ]);
    exit;
}

