<?php
/**
 * API para Avaliar Candidato (1-5 estrelas)
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

startSecureSession();
requireAuth(USER_TYPE_COMPANY);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

$application_id = isset($_POST['application_id']) ? (int)$_POST['application_id'] : 0;
$avaliacao = isset($_POST['avaliacao']) ? (int)$_POST['avaliacao'] : 0;

if ($application_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID da candidatura inválido']);
    exit;
}

if ($avaliacao < 1 || $avaliacao > 5) {
    http_response_code(400);
    echo json_encode(['error' => 'Avaliação deve ser entre 1 e 5']);
    exit;
}

$pdo = getDBConnection();
if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro de conexão']);
    exit;
}

try {
    // Verificar permissões
    $stmt = $pdo->prepare("
        SELECT a.*, j.company_id, j.titulo as vaga_titulo
        FROM applications a
        INNER JOIN jobs j ON a.job_id = j.id
        WHERE a.id = ?
    ");
    $stmt->execute([$application_id]);
    $application = $stmt->fetch();
    
    if (!$application) {
        http_response_code(404);
        echo json_encode(['error' => 'Candidatura não encontrada']);
        exit;
    }
    
    $currentUser = getCurrentUser();
    if ($currentUser['id'] != $application['company_id']) {
        http_response_code(403);
        echo json_encode(['error' => 'Sem permissão']);
        exit;
    }
    
    // Atualizar avaliação
    $stmt = $pdo->prepare("
        UPDATE applications 
        SET avaliacao = ?, avaliado_em = NOW(), updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$avaliacao, $application_id]);
    
    // Criar notificação
    $stmt = $pdo->prepare("
        INSERT INTO notifications (user_id, tipo, titulo, mensagem, link, created_at)
        VALUES (?, 'avaliacao', ?, ?, ?, NOW())
    ");
    $titulo = "Você foi avaliado!";
    $mensagem = "Sua candidatura para a vaga '{$application['vaga_titulo']}' recebeu uma avaliação de $avaliacao estrela(s).";
    $link = BASE_URL . '/perfil-pcd.php?candidatura=' . $application_id;
    $stmt->execute([
        $application['user_id'],
        $titulo,
        $mensagem,
        $link
    ]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Avaliação registrada com sucesso',
        'avaliacao' => $avaliacao
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    error_log("Erro ao avaliar candidato: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao processar requisição']);
    exit;
}

