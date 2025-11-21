<?php
/**
 * Processamento de Candidatura para Vaga
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once 'config/constants.php';
require_once 'config/database.php';
require_once 'config/auth.php';

startSecureSession();
requireAuth(USER_TYPE_PCD);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: vagas.php');
    exit;
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

$job_id = isset($_POST['job_id']) ? (int)$_POST['job_id'] : 0;
$mensagem = sanitizeInput($_POST['mensagem'] ?? '');

$errors = [];

if ($job_id <= 0) {
    $errors[] = "ID da vaga inválido.";
}

$currentUser = getCurrentUser();
$user_id = $currentUser['id'] ?? null;

if (!$user_id) {
    $errors[] = "Usuário não autenticado.";
}

// Verificar se já existe candidatura
$pdo = getDBConnection();
if (!$pdo) {
    $errors[] = "Erro de conexão com banco de dados.";
} else {
    try {
        $stmt = $pdo->prepare("SELECT id, created_at FROM applications WHERE user_id = ? AND job_id = ?");
        $stmt->execute([$user_id, $job_id]);
        $existingApplication = $stmt->fetch();
        if ($existingApplication) {
            $dataCandidatura = new DateTime($existingApplication['created_at']);
            $dataFormatada = $dataCandidatura->format('d/m/Y');
            $horaFormatada = $dataCandidatura->format('H:i');
            $errors[] = "Você já se candidatou para esta vaga em {$dataFormatada} às {$horaFormatada}.";
        }
        
        // Verificar se a vaga existe e está ativa
        $stmt = $pdo->prepare("SELECT id, status FROM jobs WHERE id = ?");
        $stmt->execute([$job_id]);
        $job = $stmt->fetch();
        
        if (!$job) {
            $errors[] = "Vaga não encontrada.";
        } elseif ($job['status'] !== 'ativa') {
            $errors[] = "Esta vaga não está mais disponível.";
        }
    } catch (PDOException $e) {
        error_log("Erro ao verificar candidatura: " . $e->getMessage());
        $errors[] = "Erro ao processar candidatura.";
    }
}

// Processar upload de currículo
$curriculo_path = null;
if (isset($_FILES['curriculo']) && $_FILES['curriculo']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['curriculo'];
    $allowed_types = ['application/pdf'];
    
    // Verificar tipo de arquivo por extensão e MIME type
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Verificar MIME type se disponível
    $file_mime = null;
    if (function_exists('mime_content_type') && file_exists($file['tmp_name'])) {
        $file_mime = @mime_content_type($file['tmp_name']);
    } elseif (isset($file['type'])) {
        $file_mime = $file['type'];
    }
    
    // Validar: deve ser PDF por extensão OU por MIME type
    if ($file_extension !== 'pdf') {
        if (!$file_mime || !in_array($file_mime, $allowed_types)) {
            $errors[] = "Tipo de arquivo inválido. Apenas PDF é permitido. Tipo recebido: " . ($file_mime ?: 'desconhecido');
        }
    }
    
    if ($file['size'] > MAX_DOCUMENTO_SIZE) {
        $errors[] = "Arquivo muito grande. Tamanho máximo: " . (MAX_DOCUMENTO_SIZE / 1024 / 1024) . "MB.";
    }
    
    if (empty($errors)) {
        $upload_dir = UPLOADS_PATH . '/curriculos/';
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                $errors[] = "Erro ao criar diretório de uploads.";
            }
        }
        
        if (empty($errors)) {
            $file_name = uniqid('curriculo_') . '_' . time() . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                $curriculo_path = 'uploads/curriculos/' . $file_name;
            } else {
                $errors[] = "Erro ao fazer upload do currículo. Verifique as permissões do diretório.";
            }
        }
    }
} elseif (isset($_FILES['curriculo']) && $_FILES['curriculo']['error'] !== UPLOAD_ERR_NO_FILE) {
    // Erro no upload (exceto quando não há arquivo)
    $upload_errors = [
        UPLOAD_ERR_INI_SIZE => 'Arquivo excede o tamanho máximo permitido pelo servidor.',
        UPLOAD_ERR_FORM_SIZE => 'Arquivo excede o tamanho máximo permitido pelo formulário.',
        UPLOAD_ERR_PARTIAL => 'Upload parcial do arquivo.',
        UPLOAD_ERR_NO_TMP_DIR => 'Diretório temporário não encontrado.',
        UPLOAD_ERR_CANT_WRITE => 'Falha ao escrever arquivo no disco.',
        UPLOAD_ERR_EXTENSION => 'Upload bloqueado por extensão.'
    ];
    $error_code = $_FILES['curriculo']['error'];
    $errors[] = $upload_errors[$error_code] ?? 'Erro desconhecido no upload do arquivo.';
}

if (!empty($errors)) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => implode(' ', $errors),
        'errors' => $errors
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Inserir candidatura
try {
    $stmt = $pdo->prepare("
        INSERT INTO applications (user_id, job_id, mensagem, curriculo_path, status)
        VALUES (:user_id, :job_id, :mensagem, :curriculo_path, 'pendente')
    ");
    
    $stmt->execute([
        ':user_id' => $user_id,
        ':job_id' => $job_id,
        ':mensagem' => !empty($mensagem) ? $mensagem : null,
        ':curriculo_path' => $curriculo_path
    ]);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Candidatura enviada com sucesso!',
        'id' => $pdo->lastInsertId()
    ], JSON_UNESCAPED_UNICODE);
    exit;
    
} catch (PDOException $e) {
    error_log("Erro ao processar candidatura: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Erro ao processar candidatura. Tente novamente.',
        'details' => $e->getMessage() // Em desenvolvimento, mostrar detalhes
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

