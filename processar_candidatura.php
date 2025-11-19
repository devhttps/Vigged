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
$user_id = $currentUser['user_id'] ?? null;

if (!$user_id) {
    $errors[] = "Usuário não autenticado.";
}

// Verificar se já existe candidatura
$pdo = getDBConnection();
if (!$pdo) {
    $errors[] = "Erro de conexão com banco de dados.";
} else {
    try {
        $stmt = $pdo->prepare("SELECT id FROM applications WHERE user_id = ? AND job_id = ?");
        $stmt->execute([$user_id, $job_id]);
        if ($stmt->fetch()) {
            $errors[] = "Você já se candidatou para esta vaga.";
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
    
    if (!in_array($file['type'], ALLOWED_DOC_TYPES)) {
        $errors[] = "Tipo de arquivo inválido. Apenas PDF é permitido.";
    }
    
    if ($file['size'] > MAX_DOCUMENTO_SIZE) {
        $errors[] = "Arquivo muito grande. Tamanho máximo: 10MB.";
    }
    
    if (empty($errors)) {
        $upload_dir = UPLOADS_PATH . '/curriculos/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('curriculo_') . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $curriculo_path = 'uploads/curriculos/' . $file_name;
        } else {
            $errors[] = "Erro ao fazer upload do currículo.";
        }
    }
}

if (!empty($errors)) {
    $_SESSION['candidatura_errors'] = $errors;
    header('Location: vagas.php?id=' . $job_id);
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
    
    $_SESSION['candidatura_success'] = true;
    header('Location: perfil-pcd.php?candidatura=success');
    exit;
    
} catch (PDOException $e) {
    error_log("Erro ao processar candidatura: " . $e->getMessage());
    $_SESSION['candidatura_errors'] = ['Erro ao processar candidatura. Tente novamente.'];
    header('Location: vagas.php?id=' . $job_id);
    exit;
}

