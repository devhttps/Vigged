<?php
/**
 * API para obter dados do candidato PCD logado
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../config/auth.php';

startSecureSession();
requireAuth(USER_TYPE_PCD);

header('Content-Type: application/json');

$currentUser = getCurrentUser();
$user_id = $currentUser['user_id'] ?? null;

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
    // Buscar dados do usuário
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $usuario = $stmt->fetch();
    
    if (!$usuario) {
        http_response_code(404);
        echo json_encode(['error' => 'Usuário não encontrado']);
        exit;
    }
    
    // Buscar candidaturas
    $stmt = $pdo->prepare("
        SELECT 
            a.*,
            j.titulo as vaga_titulo,
            j.localizacao as vaga_localizacao,
            j.tipo_contrato as vaga_tipo_contrato,
            c.nome_fantasia as empresa_nome
        FROM applications a
        INNER JOIN jobs j ON a.job_id = j.id
        INNER JOIN companies c ON j.company_id = c.id
        WHERE a.user_id = :user_id
        ORDER BY a.created_at DESC
    ");
    
    $stmt->execute([':user_id' => $user_id]);
    $candidaturas = $stmt->fetchAll();
    
    // Remover senha da resposta
    unset($usuario['senha']);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'usuario' => $usuario,
            'candidaturas' => $candidaturas,
            'total_candidaturas' => count($candidaturas),
            'candidaturas_pendentes' => count(array_filter($candidaturas, fn($c) => $c['status'] === 'pendente'))
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Erro ao buscar dados do PCD: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar dados']);
    exit;
}

