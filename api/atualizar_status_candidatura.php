<?php
/**
 * API para Atualizar Status de Candidatura com Feedback e Avaliação
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
$status = $_POST['status'] ?? '';
$feedback = $_POST['feedback'] ?? null;
$avaliacao = isset($_POST['avaliacao']) ? (int)$_POST['avaliacao'] : null;

if ($application_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'ID da candidatura inválido']);
    exit;
}

$statuses_validos = ['pendente', 'em_analise', 'aprovada', 'rejeitada', 'cancelada'];
if (!in_array($status, $statuses_validos)) {
    http_response_code(400);
    echo json_encode(['error' => 'Status inválido']);
    exit;
}

// Se reprovado, feedback é obrigatório
if ($status === 'rejeitada' && empty($feedback)) {
    http_response_code(400);
    echo json_encode(['error' => 'Feedback é obrigatório ao reprovar candidatura']);
    exit;
}

// Validar avaliação (1-5)
if ($avaliacao !== null && ($avaliacao < 1 || $avaliacao > 5)) {
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
    $pdo->beginTransaction();
    
    // Buscar candidatura e verificar permissões
    $stmt = $pdo->prepare("
        SELECT a.*, j.company_id, j.titulo as vaga_titulo, u.nome as candidato_nome, u.email as candidato_email
        FROM applications a
        INNER JOIN jobs j ON a.job_id = j.id
        INNER JOIN users u ON a.user_id = u.id
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
        echo json_encode(['error' => 'Sem permissão para gerenciar esta candidatura']);
        exit;
    }
    
    $status_anterior = $application['status'];
    
    // Registrar histórico (se a tabela existir)
    try {
        $stmt = $pdo->prepare("
            INSERT INTO application_status_history 
            (application_id, status_anterior, status_novo, feedback, avaliacao, changed_by, changed_by_type, created_at)
            VALUES (?, ?, ?, ?, ?, ?, 'company', NOW())
        ");
        $stmt->execute([
            $application_id,
            $status_anterior,
            $status,
            $feedback ?: null,
            $avaliacao,
            $currentUser['id']
        ]);
    } catch (PDOException $e) {
        // Se a tabela não existir, apenas logar o erro mas continuar
        error_log("Aviso: Não foi possível registrar histórico: " . $e->getMessage());
        // Não interromper o processo se o histórico falhar
    }
    
    // Verificar se as colunas existem antes de atualizar
    $columns = [];
    $stmt = $pdo->query("SHOW COLUMNS FROM applications");
    $existingColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Atualizar candidatura
    $updateFields = ['status = ?'];
    $updateParams = [$status];
    
    // Verificar se a coluna feedback existe antes de tentar atualizar
    if (in_array('feedback', $existingColumns)) {
        if (!empty($feedback)) {
            $updateFields[] = 'feedback = ?';
            $updateParams[] = $feedback;
        } elseif ($status === 'rejeitada') {
            // Se reprovado, garantir que feedback seja salvo mesmo se vazio
            $updateFields[] = 'feedback = ?';
            $updateParams[] = $feedback ?: '';
        }
    }
    
    // Verificar se as colunas avaliacao e avaliado_em existem
    if (in_array('avaliacao', $existingColumns) && $avaliacao !== null && $avaliacao !== '') {
        $updateFields[] = 'avaliacao = ?';
        $updateParams[] = (int)$avaliacao;
        
        if (in_array('avaliado_em', $existingColumns)) {
            $updateFields[] = 'avaliado_em = NOW()';
        }
    }
    
    $updateParams[] = $application_id;
    
    $stmt = $pdo->prepare("
        UPDATE applications 
        SET " . implode(', ', $updateFields) . ", updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute($updateParams);
    
    // Criar notificação para o candidato
    $status_labels = [
        'pendente' => 'Pendente',
        'em_analise' => 'Em Análise',
        'aprovada' => 'Aprovada',
        'rejeitada' => 'Reprovada',
        'cancelada' => 'Cancelada'
    ];
    
    $titulo = "Status da candidatura atualizado";
    $mensagem = "Sua candidatura para a vaga '{$application['vaga_titulo']}' foi atualizada para: {$status_labels[$status]}";
    
    if ($feedback) {
        $mensagem .= "\n\nFeedback: $feedback";
    }
    
    $stmt = $pdo->prepare("
        INSERT INTO notifications (user_id, tipo, titulo, mensagem, link, created_at)
        VALUES (?, ?, ?, ?, ?, NOW())
    ");
    $link = BASE_URL . '/perfil-pcd.php?candidatura=' . $application_id;
    $stmt->execute([
        $application['user_id'],
        'status', // tipo
        $titulo,
        $mensagem,
        $link
    ]);
    
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Status atualizado com sucesso',
        'data' => [
            'status' => $status,
            'feedback' => $feedback,
            'avaliacao' => $avaliacao
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Erro ao atualizar status: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao processar requisição',
        'details' => $e->getMessage() // Em desenvolvimento, mostrar detalhes
    ], JSON_UNESCAPED_UNICODE);
    exit;
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Erro geral ao atualizar status: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao processar requisição',
        'details' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

